<script setup lang="ts">
import { computed } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import FormFillLayout from '@/layouts/FormFillLayout.vue'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { routes } from '@/lib/routes'
import { CalendarClock, CheckCircle2, Circle, CircleDot, LogOut, MapPin, Users } from 'lucide-vue-next'

defineOptions({ layout: FormFillLayout })

interface TimelineItem {
    key: string
    label: string
    status: 'completed' | 'current' | 'upcoming'
    note?: string | null
}

interface TrackingPayload {
    application: {
        registration_number: string
        full_name: string
        nim: string
        semester: number
        stage: string
        stage_label: string
        result: string
        result_label: string
        revision_required: boolean
        primary_division: string | null
        secondary_division: string | null
        submitted_at: string | null
    }
    period: { name: string | null }
    timeline: TimelineItem[]
    interview: {
        scheduled_at: string
        location: string
        room: string
        status: string
    } | null
    queue: {
        queue_number: number
        status: string
    } | null
    final: {
        membership_type: string | null
        final_division: string | null
        public_message: string | null
        result: string
        result_label: string
    } | null
}

const props = defineProps<{
    tracking: TrackingPayload
    logoutUrl: string
}>()

const submittedLabel = computed(() => {
    if (!props.tracking.application.submitted_at) return null
    return new Date(props.tracking.application.submitted_at).toLocaleString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    })
})

const interviewSchedule = computed(() => {
    if (!props.tracking.interview?.scheduled_at) return null
    return new Date(props.tracking.interview.scheduled_at).toLocaleString('id-ID', {
        weekday: 'long',
        day: 'numeric',
        month: 'long',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    })
})

function timelineIcon(status: TimelineItem['status']) {
    if (status === 'completed') return CheckCircle2
    if (status === 'current') return CircleDot
    return Circle
}

function logout() {
    router.post(props.logoutUrl)
}
</script>

<template>
    <Head title="Progress OpRec" />

    <div class="mx-auto max-w-2xl space-y-6 px-2 pb-8">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <p class="text-primary text-xs font-semibold tracking-wide uppercase">Tracking OpRec</p>
                <h1 class="text-2xl font-bold">{{ tracking.application.full_name }}</h1>
                <p class="text-muted-foreground mt-1 font-mono text-sm">
                    {{ tracking.application.registration_number }}
                </p>
                <p v-if="tracking.period.name" class="text-muted-foreground text-sm">
                    {{ tracking.period.name }}
                </p>
            </div>
            <Button variant="outline" size="sm" @click="logout">
                <LogOut class="mr-2 size-4" />
                Keluar
            </Button>
        </div>

        <Card class="rounded-2xl border-border/70">
            <CardHeader class="pb-2">
                <CardTitle class="text-base">Status saat ini</CardTitle>
            </CardHeader>
            <CardContent class="space-y-2">
                <p class="text-lg font-medium">{{ tracking.application.stage_label }}</p>
                <p class="text-muted-foreground text-sm">{{ tracking.application.result_label }}</p>
                <p v-if="submittedLabel" class="text-muted-foreground text-xs">
                    Dikirim: {{ submittedLabel }}
                </p>
                <div class="text-muted-foreground flex flex-wrap gap-4 pt-2 text-sm">
                    <span>Divisi utama: {{ tracking.application.primary_division ?? '—' }}</span>
                    <span v-if="tracking.application.secondary_division">
                        Cadangan: {{ tracking.application.secondary_division }}
                    </span>
                </div>
            </CardContent>
        </Card>

        <Card class="rounded-2xl border-border/70">
            <CardHeader class="pb-2">
                <CardTitle class="text-base">Timeline proses</CardTitle>
            </CardHeader>
            <CardContent class="space-y-4">
                <div
                    v-for="item in tracking.timeline"
                    :key="item.key"
                    class="flex gap-3"
                >
                    <component
                        :is="timelineIcon(item.status)"
                        class="mt-0.5 size-5 shrink-0"
                        :class="{
                            'text-primary': item.status === 'current',
                            'text-emerald-600': item.status === 'completed',
                            'text-muted-foreground/50': item.status === 'upcoming',
                        }"
                    />
                    <div class="min-w-0 flex-1">
                        <p
                            class="font-medium"
                            :class="item.status === 'upcoming' ? 'text-muted-foreground' : 'text-foreground'"
                        >
                            {{ item.label }}
                        </p>
                        <p v-if="item.note" class="text-amber-700 dark:text-amber-400 mt-1 text-sm">
                            {{ item.note }}
                        </p>
                    </div>
                </div>
            </CardContent>
        </Card>

        <Card v-if="tracking.interview" class="rounded-2xl border-border/70">
            <CardHeader class="pb-2">
                <CardTitle class="flex items-center gap-2 text-base">
                    <CalendarClock class="size-5" />
                    Jadwal interview
                </CardTitle>
            </CardHeader>
            <CardContent class="space-y-2 text-sm">
                <p v-if="interviewSchedule" class="font-medium">{{ interviewSchedule }}</p>
                <p class="text-muted-foreground flex items-center gap-2">
                    <MapPin class="size-4 shrink-0" />
                    {{ tracking.interview.location }} · Ruang {{ tracking.interview.room }}
                </p>
                <p class="text-muted-foreground">Status: {{ tracking.interview.status }}</p>
            </CardContent>
        </Card>

        <Card v-if="tracking.queue" class="rounded-2xl border-border/70">
            <CardHeader class="pb-2">
                <CardTitle class="flex items-center gap-2 text-base">
                    <Users class="size-5" />
                    Antrean interview
                </CardTitle>
            </CardHeader>
            <CardContent>
                <p class="text-2xl font-semibold tabular-nums">#{{ tracking.queue.queue_number }}</p>
                <p class="text-muted-foreground text-sm">Status: {{ tracking.queue.status }}</p>
            </CardContent>
        </Card>

        <Card v-if="tracking.final" class="rounded-2xl border-border/70">
            <CardHeader class="pb-2">
                <CardTitle class="text-base">Keputusan akhir</CardTitle>
            </CardHeader>
            <CardContent class="space-y-2 text-sm">
                <p class="font-medium">{{ tracking.final.result_label }}</p>
                <p v-if="tracking.final.membership_type">
                    Keanggotaan: {{ tracking.final.membership_type }}
                </p>
                <p v-if="tracking.final.final_division">
                    Divisi: {{ tracking.final.final_division }}
                </p>
                <p v-if="tracking.final.public_message" class="text-muted-foreground">
                    {{ tracking.final.public_message }}
                </p>
            </CardContent>
        </Card>

        <div class="text-center">
            <Button as-child variant="link">
                <Link :href="routes.openRecruitment.landing">Kembali ke landing OpRec</Link>
            </Button>
        </div>
    </div>
</template>
