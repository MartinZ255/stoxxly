<script setup lang="ts">
import { ref, onMounted, onBeforeUnmount } from 'vue';
import {
    createChart,
    IChartApi,
    ISeriesApi,
    CandlestickData,
    UTCTimestamp,
    CandlestickSeries,
} from 'lightweight-charts';
import { route } from 'ziggy-js';

const dashboardUrl = route('dashboard');
const stocksUrl = route('stocks');

// 🔹 Auswahloptionen
const symbols = ['BTCUSDT', 'ETHUSDT', 'BNBUSDT', 'XRPUSDT', 'SOLUSDT'];
const intervals = ['1m', '3m', '5m', '15m', '30m', '1h', '2h', '4h', '12h', '1d'];

const selectedSymbol = ref('BTCUSDT');
const selectedInterval = ref('1m');

let chart: IChartApi | null = null;
let candleSeries: ISeriesApi<'Candlestick'> | null = null;
let ws: WebSocket | null = null;

import type { Time, BusinessDay } from 'lightweight-charts';

// helper
function formatTimeBerlin(time: Time, showSeconds: boolean) {
    let date: Date;
    if (typeof time === 'object' && 'year' in time) {
        // BusinessDay (Tageskerzen)
        const bd = time as BusinessDay;
        // UTC-Midnight -> danach als Berlin anzeigen
        date = new Date(Date.UTC(bd.year, (bd.month ?? 1) - 1, bd.day ?? 1));
    } else {
        // UTCTimestamp (Sekunden)
        const ts = time as number;
        date = new Date(ts * 1000);
    }
    return date.toLocaleString('de-DE', {
        timeZone: 'Europe/Berlin',
        year: '2-digit',
        month: '2-digit',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit',
        second: showSeconds ? '2-digit' : undefined,
        hour12: false,
    });
}

function initChart(container: HTMLElement) {
    const showSeconds = selectedInterval.value === '1s' || selectedInterval.value === '1m';

    chart = createChart(container, {
        width: container.clientWidth,
        height: container.clientHeight,
        layout: {
            textColor: '#d1d4dc',
            background: { color: '#1e1e1e' },
        },
        grid: {
            vertLines: { color: '#2b2b43' },
            horzLines: { color: '#2b2b43' },
        },
        timeScale: {
            borderColor: '#888',
            timeVisible: true,
            secondsVisible: showSeconds,
            // ⬇️ Labels nur auf jedem 3. Tick
            tickMarkFormatter: ((count => (t: Time) => {
                count++;
                if (count % 2 !== 0) return '';
                return formatTimeBerlin(t, showSeconds);
            })(0)),
        },
        localization: {
            locale: 'de-DE',
            timeFormatter: (t: Time) => formatTimeBerlin(t, showSeconds), // Hover/Crosshair
        },
    });

    candleSeries = chart.addSeries(CandlestickSeries, {
        upColor: '#26a69a',
        downColor: '#ef5350',
        borderVisible: false,
        wickUpColor: '#26a69a',
        wickDownColor: '#ef5350',
    });
}

// 🔹 Historische Daten laden (bis zu maxBars)
async function loadAllHistory(symbol: string, interval: string, maxBars = 10000) {
    if (!candleSeries) return;

    let allBars: CandlestickData[] = [];
    let startTime = 0;
    const limit = 1000;

    while (allBars.length < maxBars) {
        const url = `https://api.binance.com/api/v3/klines?symbol=${symbol}&interval=${interval}&limit=${limit}${
            startTime > 0 ? `&startTime=${startTime}` : ''
        }`;
        const res = await fetch(url);
        const data = await res.json();

        if (data.length === 0) break;

        const bars: CandlestickData[] = data.map((d: any) => ({
            time: Math.floor(d[0] / 1000) as UTCTimestamp,
            open: parseFloat(d[1]),
            high: parseFloat(d[2]),
            low: parseFloat(d[3]),
            close: parseFloat(d[4]),
        }));

        allBars = allBars.concat(bars);

        // neuen Startpunkt setzen = letzter "close time" + 1 ms
        startTime = data[data.length - 1][6] + 1;

        if (data.length < limit) break; // keine weiteren Daten
    }

    // nur die letzten maxBars behalten
    if (allBars.length > maxBars) {
        allBars = allBars.slice(allBars.length - maxBars);
    }

    candleSeries.setData(allBars);
    console.log(`Geladen: ${allBars.length} Kerzen für ${symbol} @ ${interval}`);
}

// 🔹 WebSocket Verbindung
function connectWs() {
    if (ws) {
        ws.close();
        ws = null;
    }

    const url = `wss://stream.binance.com:9443/ws/${selectedSymbol.value.toLowerCase()}@kline_${selectedInterval.value}`;
    ws = new WebSocket(url);

    ws.onmessage = (event) => {
        const data = JSON.parse(event.data);
        if (!candleSeries) return;

        const k = data.k;
        const bar: CandlestickData = {
            time: Math.floor(k.t / 1000) as UTCTimestamp,
            open: parseFloat(k.o),
            high: parseFloat(k.h),
            low: parseFloat(k.l),
            close: parseFloat(k.c),
        };

        candleSeries.update(bar);
    };

    ws.onclose = () => {
        console.log('WebSocket closed. Reconnecting in 5s...');
        setTimeout(() => {
            loadAllHistory(selectedSymbol.value, selectedInterval.value).then(connectWs);
        }, 5000);
    };
}

// 🔹 Symbol/Intervall neu laden
async function reloadData() {
    if (!chart) return;
    chart.remove();
    const container = document.getElementById('chart-container');
    if (!container) return;

    initChart(container);
    await loadAllHistory(selectedSymbol.value, selectedInterval.value);
    connectWs();
}

onMounted(async () => {
    const container = document.getElementById('chart-container');
    if (!container) return;

    initChart(container);
    await loadAllHistory(selectedSymbol.value, selectedInterval.value);
    connectWs();

    window.addEventListener('resize', () => {
        if (chart && container) {
            chart.applyOptions({ width: container.clientWidth });
        }
    });
});

onBeforeUnmount(() => {
    ws?.close();
    ws = null;
    chart?.remove();
    chart = null;
});
</script>

<template>
    <div class="stocks-page">
        <h1 style="font-size:32px;color:rgb(29,204,204);">Stoxxly - Wir tracken dein Casino-Portfolio</h1>

        <div class="controls">
            <label>
                Symbol:
                <select v-model="selectedSymbol" @change="reloadData">
                    <option v-for="s in symbols" :key="s" :value="s">{{ s }}</option>
                </select>
            </label>

            <label>
                Intervall:
                <select v-model="selectedInterval" @change="reloadData">
                    <option v-for="i in intervals" :key="i" :value="i">{{ i }}</option>
                </select>
            </label>
        </div>

        <div id="chart-container" style="width: 100%; height: 500px;"></div>

        <div class="links">
            <a :href="dashboardUrl">🔙 Dashboard</a>
            <a :href="stocksUrl">📊 Stocks</a>
        </div>
    </div>
</template>

<style scoped>
.stocks-page {
    padding: 1rem;
    background: #121212;
    color: #d1d4dc;
    font-family: Arial, sans-serif;
}

.controls {
    display: flex;
    gap: 1rem;
    margin-bottom: 1rem;
}

select {
    margin-left: 0.5rem;
    padding: 0.25rem;
    background: #1e1e1e;
    color: #d1d4dc;
    border: 1px solid #2b2b43;
    border-radius: 4px;
}

.links {
    margin-top: 1rem;
    display: flex;
    gap: 1rem;
}

a {
    color: #4fa3ff;
    text-decoration: none;
}
a:hover {
    text-decoration: underline;
}
</style>
