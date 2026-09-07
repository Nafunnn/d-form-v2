<script setup lang="ts">
import { computed, onMounted } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import DashboardLayout from '@/layouts/DashboardLayout.vue'
import PageHeader from '@/components/modules/dashboard/PageHeader.vue'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import { routes } from '@/lib/routes'
import { setTopbar } from '@/utils/composables/useDashboardTopbar'
import useAuth from '@/utils/composables/useAuth'
import { usePage } from '@inertiajs/vue3'
import { CalendarRange, Layers, Users } from 'lucide-vue-next'

defineOptions({ layout: DashboardLayout })

interface PeriodSummary {
    id: string
    name: string
    status: string
    status_label: string
}

interface Stats {
    total_applicants: number
    pending_screening: number
    in_screening: number
    passed_screening: number
    rejected_applicants: number
    in_interview: number
    final_review: number
    completed: number
}

const props = defineProps<{
    summary: {
        active_period: PeriodSummary | null
        stats: Stats
    }
}>()

const page = usePage()
const user = useAuth(page.props)
const canManagePeriods = computed(() => user.value?.can_manage_recruitment_periods === true)

const canListApplications = computed(() => user.value?.can_list_recruitment_applications === true)

onMounted(() => {
    setTopbar({ title: 'Rekrutmen', subtitle: 'OpenRecruitment DOSCOM' })
})

const statCards = computed(() => [
    { label: 'Total applicant', value: props.summary.stats.total_applicants },
    { label: 'Menunggu screening', value: props.summary.stats.pending_screening },
    { label: 'Dalam screening', value: props.summary.stats.in_screening },
    { label: 'Lolos screening', value: props.summary.stats.passed_screening },
    { label: 'Ditolak', value: props.summary.stats.rejected_applicants },
    { label: 'Tahap interview', value: props.summary.stats.in_interview },
    { label: 'Final review', value: props.summary.stats.final_review },
    { label: 'Selesai', value: props.summary.stats.completed },
])
</script>

<template>
    <Head title="Rekrutmen" />

    <div class="flex flex-col gap-8 md:gap-10">
        <PageHeader
            title="Rekrutmen"
            subtitle="Dashboard OpenRecruitment — pantau progress periode aktif."
            :back-href="routes.dashboard.index"
        >
            <template v-if="canManagePeriods" #actions>
                <Button as-child variant="outline" size="sm">
                    <Link :href="routes.admin.recruitment.divisions.index">
                        <Layers class="mr-2 size-4" />
                        Divisi
                    </Link>
                </Button>
                <Button as-child size="sm">
                    <Link :href="routes.admin.recruitment.periods.create">
                        <CalendarRange class="mr-2 size-4" />
                        Periode baru
                    </Link>
                </Button>
            </template>
        </PageHeader>

        <Card v-if="summary.active_period" class="rounded-2xl border-border/70">
            <CardHeader class="pb-2">
                <CardTitle class="text-base font-semibold">Periode aktif</CardTitle>
            </CardHeader>
            <CardContent class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <p class="text-lg font-medium">{{ summary.active_period.name }}</p>
                    <p class="text-muted-foreground text-sm">
                        Status: {{ summary.active_period.status_label }}
                    </p>
                </div>
                <Button v-if="canManagePeriods" as-child variant="secondary" size="sm">
                    <Link :href="routes.admin.recruitment.periods.show(summary.active_period.id)">
                        Kelola periode
                    </Link>
                </Button>
            </CardContent>
        </Card>

        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <Card v-for="card in statCards" :key="card.label" class="rounded-2xl border-border/70">
                <CardContent class="flex items-center gap-4 p-5">
                    <div class="bg-primary/10 text-primary flex size-10 items-center justify-center rounded-xl">
                        <Users class="size-5" />
                    </div>
                    <div>
                        <p class="text-muted-foreground text-xs font-medium uppercase tracking-wide">
                            {{ card.label }}
                        </p>
                        <p class="text-2xl font-semibold tabular-nums">{{ card.value }}</p>
                    </div>
                </CardContent>
            </Card>
        </div>

        <Card v-if="canListApplications" class="rounded-2xl border-dashed border-border/70">
            <CardContent class="flex flex-wrap items-center justify-between gap-4 p-6">
                <div>
                    <p class="font-medium">Kelola applicant</p>
                    <p class="text-muted-foreground text-sm">
                        Tinjau pendaftaran, unduh dokumen, dan ambil keputusan screening.
                    </p>
                </div>
                <Button as-child>
                    <Link :href="routes.admin.recruitment.applications.index">Daftar applicant</Link>
                </Button>
            </CardContent>
        </Card>

        <Card v-if="canManagePeriods" class="rounded-2xl border-dashed border-border/70">
            <CardContent class="flex flex-wrap items-center justify-between gap-4 p-6">
                <div>
                    <p class="font-medium">Kelola periode & divisi</p>
                    <p class="text-muted-foreground text-sm">
                        Buat periode OpRec, atur jadwal pendaftaran, dan assign interviewer per divisi.
                    </p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <Button as-child variant="outline">
                        <Link :href="routes.admin.recruitment.periods.index">Semua periode</Link>
                    </Button>
                    <Button as-child variant="outline">
                        <Link :href="routes.admin.recruitment.divisions.index">Divisi & interviewer</Link>
                    </Button>
                </div>
            </CardContent>
        </Card>
    </div>
</template>
