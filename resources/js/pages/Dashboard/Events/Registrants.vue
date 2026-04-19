<script setup lang="ts">
import { ref, computed } from 'vue'
import { Head } from '@inertiajs/vue3'
import { toast } from 'vue-sonner'
import DashboardLayout from '@/layouts/DashboardLayout.vue'
import PageHeader from '@/components/modules/dashboard/PageHeader.vue'
import EmptyState from '@/components/modules/dashboard/EmptyState.vue'
import { Card, CardContent } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import { Badge } from '@/components/ui/badge'
import { Input } from '@/components/ui/input'
import { Avatar, AvatarFallback } from '@/components/ui/avatar'
import { Tabs, TabsList, TabsTrigger } from '@/components/ui/tabs'
import { Dialog, DialogContent, DialogDescription, DialogHeader, DialogTitle } from '@/components/ui/dialog'
import ConfirmationModal from '@/components/core/ConfirmationModal.vue'
import { Search, CheckCircle, XCircle, Eye } from 'lucide-vue-next'
import { dummyRegistrants, formatDateTime } from '@/lib/dummyData'

defineOptions({ layout: DashboardLayout })

const searchQuery = ref('')
const activeStatusTab = ref('all')
const registrants = ref([...dummyRegistrants])
const selectedRegistrant = ref<IRegistrant | null>(null)
const showDetailModal = ref(false)
const showApproveModal = ref(false)
const showRejectModal = ref(false)
const actionTarget = ref<IRegistrant | null>(null)

const filteredRegistrants = computed(() => {
    let list = registrants.value

    if (activeStatusTab.value !== 'all') {
        list = list.filter(r => r.status === activeStatusTab.value)
    }

    if (searchQuery.value.trim()) {
        const q = searchQuery.value.toLowerCase()
        list = list.filter(r =>
            r.user.name.toLowerCase().includes(q) ||
            r.user.email.toLowerCase().includes(q),
        )
    }

    return list
})

const statusCounts = computed(() => ({
    all: registrants.value.length,
    pending: registrants.value.filter(r => r.status === 'pending').length,
    approved: registrants.value.filter(r => r.status === 'approved').length,
    rejected: registrants.value.filter(r => r.status === 'rejected').length,
}))

function openDetail(reg: IRegistrant) { selectedRegistrant.value = reg; showDetailModal.value = true }
function startApprove(reg: IRegistrant) { actionTarget.value = reg; showApproveModal.value = true }
function startReject(reg: IRegistrant) { actionTarget.value = reg; showRejectModal.value = true }

function confirmApprove() {
    if (actionTarget.value) {
        actionTarget.value.status = 'approved'
        toast.success(`Approved ${actionTarget.value.user.name}. Approval email will be sent.`)
    }
    showApproveModal.value = false
}

function confirmReject() {
    if (actionTarget.value) {
        actionTarget.value.status = 'rejected'
        toast.success(`Rejected ${actionTarget.value.user.name}. Rejection email will be sent.`)
    }
    showRejectModal.value = false
}

function getInitials(name: string): string {
    return name.split(' ').map(w => w[0]).join('').toUpperCase().slice(0, 2)
}

const statusVariant = (s: string) => {
    if (s === 'approved') return 'default' as const
    if (s === 'rejected') return 'destructive' as const
    return 'secondary' as const
}
</script>

<template>
    <Head title="Registrants" />

    <div class="flex flex-col gap-6">
        <PageHeader title="Registrants" subtitle="Manage event registrations and approvals." backHref="/dashboard/events" />

        <div class="flex flex-wrap items-center gap-3">
            <div class="relative w-full max-w-xs">
                <Search class="absolute left-3 top-1/2 size-4 -translate-y-1/2 text-muted-foreground" />
                <Input v-model="searchQuery" placeholder="Search registrants..." class="pl-9" />
            </div>
        </div>

        <Tabs v-model="activeStatusTab">
            <TabsList>
                <TabsTrigger value="all">All ({{ statusCounts.all }})</TabsTrigger>
                <TabsTrigger value="pending">Pending ({{ statusCounts.pending }})</TabsTrigger>
                <TabsTrigger value="approved">Approved ({{ statusCounts.approved }})</TabsTrigger>
                <TabsTrigger value="rejected">Rejected ({{ statusCounts.rejected }})</TabsTrigger>
            </TabsList>
        </Tabs>

        <Card v-if="filteredRegistrants.length > 0" class="overflow-hidden rounded-xl border shadow-xs">
            <CardContent class="p-0">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b bg-muted/40 text-left text-xs font-medium text-muted-foreground">
                                <th class="px-4 py-3">Name</th>
                                <th class="px-4 py-3">Email</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3">Submitted</th>
                                <th class="px-4 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="reg in filteredRegistrants" :key="reg.id" class="border-b transition-colors last:border-0 hover:bg-muted/20">
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2.5">
                                        <Avatar class="size-8">
                                            <AvatarFallback class="bg-primary/10 text-[10px] font-medium text-primary">{{ getInitials(reg.user.name) }}</AvatarFallback>
                                        </Avatar>
                                        <span class="font-medium">{{ reg.user.name }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-muted-foreground">{{ reg.user.email }}</td>
                                <td class="px-4 py-3">
                                    <Badge :variant="statusVariant(reg.status)" class="text-[10px] capitalize">{{ reg.status }}</Badge>
                                </td>
                                <td class="px-4 py-3 text-xs text-muted-foreground">{{ formatDateTime(reg.submitted_at) }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-end gap-1">
                                        <Button variant="ghost" size="icon" class="size-7" @click="openDetail(reg)"><Eye class="size-4" /></Button>
                                        <Button v-if="reg.status === 'pending'" variant="ghost" size="icon" class="size-7 text-success hover:text-success" @click="startApprove(reg)"><CheckCircle class="size-4" /></Button>
                                        <Button v-if="reg.status === 'pending'" variant="ghost" size="icon" class="size-7 text-destructive hover:text-destructive" @click="startReject(reg)"><XCircle class="size-4" /></Button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </CardContent>
        </Card>

        <EmptyState v-else title="No registrants found" description="Registrants will appear here once people start registering, or try adjusting your filters." animation-url="https://lottie.host/4e039bf3-670e-4a0f-8a6c-1bee793bfc23/JkaGBMIxOz.json" />
    </div>

    <Dialog :open="showDetailModal" @update:open="showDetailModal = $event">
        <DialogContent class="max-w-md">
            <DialogHeader>
                <DialogTitle>Registrant Details</DialogTitle>
                <DialogDescription>Form answers submitted by {{ selectedRegistrant?.user.name }}</DialogDescription>
            </DialogHeader>
            <div v-if="selectedRegistrant" class="flex flex-col gap-3 pt-2">
                <div v-for="(value, key) in selectedRegistrant.answers" :key="key" class="rounded-lg border p-3">
                    <p class="text-xs font-medium text-muted-foreground">{{ key }}</p>
                    <p class="mt-0.5 text-sm">{{ value || '—' }}</p>
                </div>
            </div>
        </DialogContent>
    </Dialog>

    <ConfirmationModal :open="showApproveModal" title="Approve Registrant" :description="`Approve ${actionTarget?.user.name}? An approval email will be sent.`" confirm-text="Approve" @confirm="confirmApprove" @cancel="showApproveModal = false" @update:open="showApproveModal = $event" />
    <ConfirmationModal :open="showRejectModal" title="Reject Registrant" :description="`Reject ${actionTarget?.user.name}? A rejection email will be sent.`" confirm-text="Reject" variant="destructive" @confirm="confirmReject" @cancel="showRejectModal = false" @update:open="showRejectModal = $event" />
</template>
