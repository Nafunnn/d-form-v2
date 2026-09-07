<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import FormFillLayout from '@/layouts/FormFillLayout.vue'
import { Button } from '@/components/ui/button'
import { Card, CardContent } from '@/components/ui/card'
import { routes } from '@/lib/routes'
import { CheckCircle2, Mail } from 'lucide-vue-next'

defineOptions({ layout: FormFillLayout })

defineProps<{
    registrationNumber: string
    periodName: string | null
    applicantName: string | null
}>()
</script>

<template>
    <Head title="Pendaftaran berhasil" />

    <div class="mx-auto max-w-xl px-2">
        <Card class="rounded-2xl border-border/70">
            <CardContent class="space-y-6 p-8 text-center">
                <div class="bg-primary/10 text-primary mx-auto flex size-14 items-center justify-center rounded-full">
                    <CheckCircle2 class="size-7" />
                </div>

                <div class="space-y-2">
                    <h1 class="text-2xl font-bold">Pendaftaran berhasil!</h1>
                    <p v-if="applicantName" class="text-muted-foreground">
                        Terima kasih, {{ applicantName }}.
                    </p>
                    <p v-if="periodName" class="text-muted-foreground text-sm">
                        Periode: {{ periodName }}
                    </p>
                </div>

                <div class="rounded-xl border border-border/70 bg-muted/30 p-4">
                    <p class="text-muted-foreground text-xs tracking-wide uppercase">Nomor pendaftaran</p>
                    <p class="mt-1 font-mono text-xl font-semibold">{{ registrationNumber }}</p>
                </div>

                <div class="text-muted-foreground flex items-start justify-center gap-2 text-left text-sm">
                    <Mail class="mt-0.5 size-4 shrink-0" />
                    <p>
                        Token tracking dan instruksi selanjutnya dikirim ke email pribadi kamu.
                        Simpan email tersebut — token tidak ditampilkan di halaman ini.
                    </p>
                </div>

                <div class="flex flex-wrap justify-center gap-3">
                    <Button as-child variant="outline">
                        <Link :href="routes.openRecruitment.landing">Kembali ke landing</Link>
                    </Button>
                </div>
            </CardContent>
        </Card>
    </div>
</template>
