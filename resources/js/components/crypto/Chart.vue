<template>
    <div
        ref="chartContainer"
        class="w-full h-[350px] bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-md transition-colors duration-300"
    ></div>
</template>


<script setup lang="ts">
import { ref, onMounted, onBeforeUnmount } from "vue"
import {
    createChart,
    IChartApi,
    ISeriesApi,
    CandlestickData,
    UTCTimestamp,
    CandlestickSeries,
    Time,
    BusinessDay,
} from "lightweight-charts"

const props = defineProps<{
    symbol: string
    interval: string
}>()

let chart: IChartApi | null = null
let candleSeries: ISeriesApi<"Candlestick"> | null = null
let ws: WebSocket | null = null

const chartContainer = ref<HTMLDivElement | null>(null)

/**
 * Layout Farben abhängig vom Darkmode
 */
function getChartColors() {
    const isDark = document.documentElement.classList.contains("dark")
    return isDark
        ? {
            layout: { textColor: "#d1d4dc", background: { color: "#1e1e1e" } },
            grid: {
                vertLines: { color: "#2b2b43" },
                horzLines: { color: "#2b2b43" },
            },
            timeScale: { borderColor: "#888" },
        }
        : {
            layout: { textColor: "#1e1e1e", background: { color: "#ffffff" } },
            grid: {
                vertLines: { color: "#e0e0e0" },
                horzLines: { color: "#e0e0e0" },
            },
            timeScale: { borderColor: "#cccccc" },
        }
}

/**
 * Zeit formatieren
 */
function formatTimeBerlin(time: Time, showSeconds: boolean) {
    let date: Date
    if (typeof time === "object" && "year" in time) {
        const bd = time as BusinessDay
        date = new Date(Date.UTC(bd.year, (bd.month ?? 1) - 1, bd.day ?? 1))
    } else {
        const ts = time as number
        date = new Date(ts * 1000)
    }
    return date.toLocaleString("de-DE", {
        timeZone: "Europe/Berlin",
        year: "2-digit",
        month: "2-digit",
        day: "2-digit",
        hour: "2-digit",
        minute: "2-digit",
        second: showSeconds ? "2-digit" : undefined,
        hour12: false,
    })
}

/**
 * Chart initialisieren
 */
function initChart(container: HTMLElement) {
    const showSeconds = props.interval === "1s" || props.interval === "1m"

    const colors = getChartColors()

    chart = createChart(container, {
        width: container.clientWidth,
        height: container.clientHeight,
        ...colors,
        timeScale: {
            ...colors.timeScale,
            timeVisible: true,
            secondsVisible: showSeconds,
            tickMarkFormatter: ((count => (t: Time) => {
                count++
                if (count % 2 !== 0) return ""
                return formatTimeBerlin(t, showSeconds)
            }))(0),
        },
        localization: {
            locale: "de-DE",
            timeFormatter: (t: Time) => formatTimeBerlin(t, showSeconds),
        },
    })

    candleSeries = chart.addSeries(CandlestickSeries, {
        upColor: "#26a69a",
        downColor: "#ef5350",
        borderVisible: false,
        wickUpColor: "#26a69a",
        wickDownColor: "#ef5350",
    })
}

/**
 * Historische Daten laden
 */
async function loadAllHistory(symbol: string, interval: string, maxBars = 1000) {
    if (!candleSeries) return

    const url = `https://api.binance.com/api/v3/klines?symbol=${symbol}&interval=${interval}&limit=${maxBars}`
    const res = await fetch(url)
    const data = await res.json()

    const bars: CandlestickData[] = data.map((d: any) => ({
        time: Math.floor(d[0] / 1000) as UTCTimestamp,
        open: parseFloat(d[1]),
        high: parseFloat(d[2]),
        low: parseFloat(d[3]),
        close: parseFloat(d[4]),
    }))

    candleSeries.setData(bars)
}

/**
 * WebSocket Verbindung
 */
function connectWs() {
    if (ws) {
        ws.close()
        ws = null
    }

    const url = `wss://stream.binance.com:9443/ws/${props.symbol.toLowerCase()}@kline_${props.interval}`
    ws = new WebSocket(url)

    ws.onmessage = (event) => {
        const data = JSON.parse(event.data)
        if (!candleSeries) return

        const k = data.k
        const bar: CandlestickData = {
            time: Math.floor(k.t / 1000) as UTCTimestamp,
            open: parseFloat(k.o),
            high: parseFloat(k.h),
            low: parseFloat(k.l),
            close: parseFloat(k.c),
        }
        candleSeries.update(bar)
    }
}

/**
 * Mount + Cleanup
 */
onMounted(async () => {
    if (!chartContainer.value) return
    initChart(chartContainer.value)
    await loadAllHistory(props.symbol, props.interval)
    connectWs()

    window.addEventListener("resize", () => {
        if (chart && chartContainer.value) {
            chart.applyOptions({ width: chartContainer.value.clientWidth })
        }
    })

    // Darkmode-Event → Chart neu rendern
    const observer = new MutationObserver(() => {
        if (chartContainer.value) {
            chart?.remove()
            initChart(chartContainer.value)
            loadAllHistory(props.symbol, props.interval)
        }
    })
    observer.observe(document.documentElement, { attributes: true, attributeFilter: ["class"] })
})

onBeforeUnmount(() => {
    ws?.close()
    ws = null
    chart?.remove()
    chart = null
})
</script>
