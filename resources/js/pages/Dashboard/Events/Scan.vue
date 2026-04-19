<script setup lang="ts">
import { ref } from 'vue'
import { Head } from '@inertiajs/vue3'
import DashboardLayout from '@/layouts/DashboardLayout.vue'
import PageHeader from '@/components/modules/dashboard/PageHeader.vue'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import { Badge } from '@/components/ui/badge'
import { Vue3Lottie } from 'vue3-lottie'
import { CheckCircle, AlertTriangle, XCircle } from 'lucide-vue-next'

defineOptions({ layout: DashboardLayout })

const scanResult = ref<null | { name: string; email: string; status: 'success' | 'already' | 'invalid' }>(null)
const scanHistory = ref([
    { name: 'Ahmad Fauzi', email: 'ahmad@student.dinus.ac.id', time: '09:15', status: 'success' as const },
    { name: 'Siti Nurhaliza', email: 'siti@student.dinus.ac.id', time: '09:12', status: 'success' as const },
    { name: 'Unknown QR', email: '-', time: '09:10', status: 'invalid' as const },
])

function simulateScan() {
    const results = [
        { name: 'Dewi Lestari', email: 'dewi@student.dinus.ac.id', status: 'success' as const },
        { name: 'Ahmad Fauzi', email: 'ahmad@student.dinus.ac.id', status: 'already' as const },
        { name: 'Invalid QR', email: '-', status: 'invalid' as const },
    ]
    scanResult.value = results[Math.floor(Math.random() * results.length)]
    scanHistory.value.unshift({
        name: scanResult.value.name, email: scanResult.value.email,
        time: new Date().toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' }),
        status: scanResult.value.status,
    })
}

const statusConfig = {
    success: { icon: CheckCircle, class: 'text-success', bg: 'bg-success/10', label: 'Checked In' },
    already: { icon: AlertTriangle, class: 'text-warning', bg: 'bg-warning/10', label: 'Already Scanned' },
    invalid: { icon: XCircle, class: 'text-destructive', bg: 'bg-destructive/10', label: 'Invalid' },
}
</script>

<template>
    <Head title="QR Scan" />

    <div class="flex flex-col gap-6">
        <PageHeader title="QR Attendance Scanner" subtitle="Scan participant QR codes for attendance." backHref="/dashboard/events" />

        <div class="grid gap-6 lg:grid-cols-2">
            <Card class="overflow-hidden rounded-xl border shadow-xs">
                <CardContent class="p-0">
                    <div class="flex aspect-square max-h-[400px] flex-col items-center justify-center bg-muted/30">
                        <Vue3Lottie
                            animation-link="https://lottie.host/b0e0bf4e-4833-4ed3-aca8-1e7c8e5e5e4e/qr-scan.json"
                            :height="160"
                            :width="160"
                            :loop="true"
                            class="mb-2 opacity-40"
                        />
                        <p class="text-sm font-medium text-muted-foreground">Camera viewfinder</p>
                        <p class="mt-1 text-xs text-muted-foreground">Point at a QR code to scan</p>
                        <Button class="mt-4" @click="simulateScan">Simulate Scan</Button>
                    </div>

                    <div v-if="scanResult" class="border-t p-4">
                        <div :class="['flex items-center gap-3 rounded-xl p-3', statusConfig[scanResult.status].bg]">
                            <component :is="statusConfig[scanResult.status].icon" :class="['size-6', statusConfig[scanResult.status].class]" />
                            <div>
                                <p class="text-sm font-semibold">{{ scanResult.name }}</p>
                                <p class="text-xs text-muted-foreground">{{ scanResult.email }}</p>
                                <Badge variant="outline" class="mt-1 text-[10px]" :class="statusConfig[scanResult.status].class">
                                    {{ statusConfig[scanResult.status].label }}
                                </Badge>
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <Card class="rounded-xl border shadow-xs">
                <CardHeader class="pb-3"><CardTitle class="text-base font-medium">Scan History</CardTitle></CardHeader>
                <CardContent class="flex flex-col gap-2 pt-0">
                    <div
                        v-for="(entry, idx) in scanHistory" :key="idx"
                        class="flex items-center justify-between rounded-lg border p-3 transition-colors hover:bg-muted/20"
                    >
                        <div class="flex items-center gap-2.5">
                            <component :is="statusConfig[entry.status].icon" :class="['size-4', statusConfig[entry.status].class]" />
                            <div>
                                <p class="text-xs font-medium">{{ entry.name }}</p>
                                <p class="text-[10px] text-muted-foreground">{{ entry.email }}</p>
                            </div>
                        </div>
                        <span class="text-[10px] tabular-nums text-muted-foreground">{{ entry.time }}</span>
                    </div>
                    <p v-if="scanHistory.length === 0" class="py-8 text-center text-sm text-muted-foreground">No scans yet.</p>
                </CardContent>
            </Card>
        </div>
    </div>
</template>
