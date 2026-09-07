<script setup lang="ts">
import { computed, onMounted, ref } from 'vue'
import { Head, router, useForm, usePage } from '@inertiajs/vue3'
import DashboardLayout from '@/layouts/DashboardLayout.vue'
import PageHeader from '@/components/modules/dashboard/PageHeader.vue'
import { Button } from '@/components/ui/button'
import { Card, CardContent } from '@/components/ui/card'
import { Label } from '@/components/ui/label'
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs'
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog'
import { routes } from '@/lib/routes'
import { setTopbar } from '@/utils/composables/useDashboardTopbar'
import useAuth from '@/utils/composables/useAuth'
import { CheckCircle2, Download, FileText, History, XCircle } from 'lucide-vue-next'

defineOptions({ layout: DashboardLayout })

type ScreeningAction = 'revision' | 'reject' | null

interface ScreeningRow {
    id: string
    decision: string
    decision_label: string
    reason: string | null
    reason_label: string | null
    notes: string | null
    acted_at: string | null
    actor: { id: string; name: string } | null
}

interface ActivityRow {
    id: string
    action: string
    old_values: Record<string, unknown> | null
    new_values: Record<string, unknown> | null
    created_at: string | null
    actor: { id: string; name: string } | null
}

interface ApplicationDetail {
    id: string
    registration_number: string
    full_name: string
    nim: string
    semester: number
    phone: string
    personal_email: string
    student_email: string
    instagram_username: string
    stage: string
    stage_label: string
    result: string
    result_label: string
    is_verified: boolean
    revision_required: boolean
    submitted_at: string | null
    period: { id: string; name: string } | null
    primary_division: { id: string; name: string; code: string } | null
    secondary_division: { id: string; name: string; code: string } | null
    document: {
        cv_original_name: string
        cv_mime: string
        cv_size_bytes: number
        portfolio_type: string
        portfolio_url: string | null
        portfolio_original_name: string | null
        portfolio_mime: string | null
        portfolio_size_bytes: number | null
        has_cv_file: boolean
        has_portfolio_file: boolean
    } | null
    screenings: ScreeningRow[]
    activity_logs: ActivityRow[]
    can_screen: boolean
}

const props = defineProps<{
    application: ApplicationDetail
    screeningReasonOptions: { value: string; label: string }[]
}>()

const page = usePage()
const user = useAuth(page.props)
const canScreen = computed(
    () => props.application.can_screen && user.value?.can_screen_recruitment_applications === true,
)

const screeningModalOpen = ref(false)
const screeningAction = ref<ScreeningAction>(null)

const screeningForm = useForm({
    reason: '',
    notes: '',
    public_message: '',
})

onMounted(() => {
    setTopbar({
        title: props.application.full_name,
        subtitle: props.application.registration_number,
    })
})

function openScreeningModal(action: ScreeningAction) {
    screeningAction.value = action
    screeningForm.reset()
    screeningForm.clearErrors()
    screeningModalOpen.value = true
}

function submitScreening() {
    if (screeningAction.value === 'revision') {
        screeningForm.post(routes.admin.recruitment.applications.screening.revision(props.application.id), {
            preserveScroll: true,
            onSuccess: () => {
                screeningModalOpen.value = false
            },
        })
        return
    }

    if (screeningAction.value === 'reject') {
        screeningForm.post(routes.admin.recruitment.applications.screening.reject(props.application.id), {
            preserveScroll: true,
            onSuccess: () => {
                screeningModalOpen.value = false
            },
        })
    }
}

function passApplication() {
    router.post(
        routes.admin.recruitment.applications.screening.pass(props.application.id),
        {},
        { preserveScroll: true },
    )
}

function formatBytes(bytes: number): string {
    if (bytes < 1024) return `${bytes} B`
    if (bytes < 1024 * 1024) return `${(bytes / 1024).toFixed(1)} KB`
    return `${(bytes / (1024 * 1024)).toFixed(1)} MB`
}

const modalTitle = computed(() => {
    if (screeningAction.value === 'revision') return 'Minta revisi'
    if (screeningAction.value === 'reject') return 'Tolak applicant'
    return 'Keputusan screening'
})
</script>

<template>
    <Head :title="application.full_name" />

    <div class="mx-auto flex max-w-5xl flex-col gap-6">
        <PageHeader
            :title="application.full_name"
            :subtitle="`${application.registration_number} · ${application.stage_label}`"
            :back-href="routes.admin.recruitment.applications.index"
        >
            <template v-if="canScreen" #actions>
                <Button size="sm" variant="outline" @click="openScreeningModal('revision')">
                    Minta revisi
                </Button>
                <Button size="sm" variant="destructive" @click="openScreeningModal('reject')">
                    <XCircle class="mr-2 size-4" />
                    Tolak
                </Button>
                <Button size="sm" @click="passApplication">
                    <CheckCircle2 class="mr-2 size-4" />
                    Lolos screening
                </Button>
            </template>
        </PageHeader>

        <div
            v-if="application.revision_required"
            class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900"
        >
            Applicant diminta melakukan revisi pendaftaran.
        </div>

        <Tabs default-value="overview" class="w-full">
            <TabsList class="grid w-full grid-cols-4">
                <TabsTrigger value="overview">Overview</TabsTrigger>
                <TabsTrigger value="documents">Dokumen</TabsTrigger>
                <TabsTrigger value="screening">Screening</TabsTrigger>
                <TabsTrigger value="history">Riwayat</TabsTrigger>
            </TabsList>

            <TabsContent value="overview" class="mt-4">
                <Card class="rounded-2xl border-border/70">
                    <CardContent class="grid gap-4 p-6 sm:grid-cols-2">
                        <div>
                            <p class="text-muted-foreground text-xs uppercase">NIM</p>
                            <p class="font-medium">{{ application.nim }}</p>
                        </div>
                        <div>
                            <p class="text-muted-foreground text-xs uppercase">Semester</p>
                            <p class="font-medium">{{ application.semester }}</p>
                        </div>
                        <div>
                            <p class="text-muted-foreground text-xs uppercase">Telepon</p>
                            <p class="font-medium">{{ application.phone }}</p>
                        </div>
                        <div>
                            <p class="text-muted-foreground text-xs uppercase">Instagram</p>
                            <p class="font-medium">@{{ application.instagram_username }}</p>
                        </div>
                        <div>
                            <p class="text-muted-foreground text-xs uppercase">Email pribadi</p>
                            <p class="font-medium">{{ application.personal_email }}</p>
                        </div>
                        <div>
                            <p class="text-muted-foreground text-xs uppercase">Email kampus</p>
                            <p class="font-medium">{{ application.student_email }}</p>
                        </div>
                        <div>
                            <p class="text-muted-foreground text-xs uppercase">Divisi utama</p>
                            <p class="font-medium">{{ application.primary_division?.name ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-muted-foreground text-xs uppercase">Divisi cadangan</p>
                            <p class="font-medium">{{ application.secondary_division?.name ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-muted-foreground text-xs uppercase">Periode</p>
                            <p class="font-medium">{{ application.period?.name ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-muted-foreground text-xs uppercase">Hasil</p>
                            <p class="font-medium">{{ application.result_label }}</p>
                        </div>
                    </CardContent>
                </Card>
            </TabsContent>

            <TabsContent value="documents" class="mt-4">
                <Card class="rounded-2xl border-border/70">
                    <CardContent class="space-y-4 p-6">
                        <div v-if="application.document" class="space-y-4">
                            <div class="flex flex-wrap items-center justify-between gap-3 rounded-xl border p-4">
                                <div class="flex items-center gap-3">
                                    <FileText class="text-muted-foreground size-5" />
                                    <div>
                                        <p class="font-medium">{{ application.document.cv_original_name }}</p>
                                        <p class="text-muted-foreground text-xs">
                                            CV · {{ formatBytes(application.document.cv_size_bytes) }}
                                        </p>
                                    </div>
                                </div>
                                <Button
                                    v-if="application.document.has_cv_file"
                                    as-child
                                    variant="outline"
                                    size="sm"
                                >
                                    <a :href="routes.admin.recruitment.applications.document(application.id, 'cv')">
                                        <Download class="mr-2 size-4" />
                                        Unduh CV
                                    </a>
                                </Button>
                            </div>

                            <div class="rounded-xl border p-4">
                                <p class="font-medium">Portfolio</p>
                                <p v-if="application.document.portfolio_type === 'url'" class="text-muted-foreground mt-1 text-sm">
                                    <a
                                        :href="application.document.portfolio_url ?? '#'"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="text-primary underline"
                                    >
                                        {{ application.document.portfolio_url }}
                                    </a>
                                </p>
                                <div
                                    v-else-if="application.document.has_portfolio_file"
                                    class="mt-2 flex flex-wrap items-center justify-between gap-3"
                                >
                                    <p class="text-sm">{{ application.document.portfolio_original_name }}</p>
                                    <Button as-child variant="outline" size="sm">
                                        <a
                                            :href="
                                                routes.admin.recruitment.applications.document(
                                                    application.id,
                                                    'portfolio',
                                                )
                                            "
                                        >
                                            <Download class="mr-2 size-4" />
                                            Unduh portfolio
                                        </a>
                                    </Button>
                                </div>
                                <p v-else class="text-muted-foreground mt-1 text-sm">Tidak ada portfolio.</p>
                            </div>
                        </div>
                        <p v-else class="text-muted-foreground text-sm">Dokumen belum tersedia.</p>
                    </CardContent>
                </Card>
            </TabsContent>

            <TabsContent value="screening" class="mt-4">
                <Card class="rounded-2xl border-border/70">
                    <CardContent class="space-y-4 p-6">
                        <div
                            v-for="screening in application.screenings"
                            :key="screening.id"
                            class="rounded-xl border p-4"
                        >
                            <div class="flex flex-wrap items-start justify-between gap-2">
                                <div>
                                    <p class="font-medium">{{ screening.decision_label }}</p>
                                    <p v-if="screening.reason_label" class="text-muted-foreground text-sm">
                                        {{ screening.reason_label }}
                                    </p>
                                </div>
                                <p class="text-muted-foreground text-xs">
                                    {{ screening.actor?.name ?? 'Staff' }}
                                </p>
                            </div>
                            <p v-if="screening.notes" class="mt-2 text-sm">{{ screening.notes }}</p>
                        </div>
                        <p v-if="application.screenings.length === 0" class="text-muted-foreground text-sm">
                            Belum ada keputusan screening.
                        </p>
                    </CardContent>
                </Card>
            </TabsContent>

            <TabsContent value="history" class="mt-4">
                <Card class="rounded-2xl border-border/70">
                    <CardContent class="space-y-4 p-6">
                        <div
                            v-for="log in application.activity_logs"
                            :key="log.id"
                            class="flex gap-3 rounded-xl border p-4"
                        >
                            <History class="text-muted-foreground mt-0.5 size-4 shrink-0" />
                            <div>
                                <p class="font-medium">{{ log.action }}</p>
                                <p class="text-muted-foreground text-xs">
                                    {{ log.actor?.name ?? 'Sistem' }}
                                </p>
                            </div>
                        </div>
                        <p v-if="application.activity_logs.length === 0" class="text-muted-foreground text-sm">
                            Belum ada aktivitas tercatat.
                        </p>
                    </CardContent>
                </Card>
            </TabsContent>
        </Tabs>
    </div>

    <Dialog v-model:open="screeningModalOpen">
        <DialogContent class="sm:max-w-md">
            <DialogHeader>
                <DialogTitle>{{ modalTitle }}</DialogTitle>
                <DialogDescription>
                    Alasan wajib diisi. Catatan tambahan diperlukan jika memilih "Lainnya".
                </DialogDescription>
            </DialogHeader>

            <form class="space-y-4" @submit.prevent="submitScreening">
                <div class="space-y-2">
                    <Label for="reason">Alasan</Label>
                    <select
                        id="reason"
                        v-model="screeningForm.reason"
                        class="border-input bg-background h-9 w-full rounded-md border px-3 text-sm"
                        required
                    >
                        <option value="" disabled>Pilih alasan</option>
                        <option
                            v-for="opt in screeningReasonOptions"
                            :key="opt.value"
                            :value="opt.value"
                        >
                            {{ opt.label }}
                        </option>
                    </select>
                    <p v-if="screeningForm.errors.reason" class="text-destructive text-xs">
                        {{ screeningForm.errors.reason }}
                    </p>
                </div>

                <div class="space-y-2">
                    <Label for="notes">Catatan</Label>
                    <textarea
                        id="notes"
                        v-model="screeningForm.notes"
                        rows="3"
                        class="border-input bg-background w-full rounded-md border px-3 py-2 text-sm"
                        placeholder="Catatan internal untuk tim..."
                    />
                    <p v-if="screeningForm.errors.notes" class="text-destructive text-xs">
                        {{ screeningForm.errors.notes }}
                    </p>
                </div>

                <div v-if="screeningAction === 'reject'" class="space-y-2">
                    <Label for="public_message">Pesan untuk applicant (opsional)</Label>
                    <textarea
                        id="public_message"
                        v-model="screeningForm.public_message"
                        rows="2"
                        class="border-input bg-background w-full rounded-md border px-3 py-2 text-sm"
                    />
                </div>

                <DialogFooter>
                    <Button type="button" variant="outline" @click="screeningModalOpen = false">
                        Batal
                    </Button>
                    <Button
                        type="submit"
                        :disabled="screeningForm.processing"
                        :variant="screeningAction === 'reject' ? 'destructive' : 'default'"
                    >
                        Simpan keputusan
                    </Button>
                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>
</template>
