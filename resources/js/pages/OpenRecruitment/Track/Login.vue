<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3'
import FormFillLayout from '@/layouts/FormFillLayout.vue'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { routes } from '@/lib/routes'

defineOptions({ layout: FormFillLayout })

const props = defineProps<{
    authenticateUrl: string
}>()

const form = useForm({
    registration_number: '',
    tracking_token: '',
})

function submit() {
    form.post(props.authenticateUrl, {
        preserveScroll: true,
    })
}
</script>

<template>
    <Head title="Tracking OpRec" />

    <div class="mx-auto max-w-md px-2">
        <div class="mb-6 space-y-2 text-center">
            <p class="text-primary text-xs font-semibold tracking-wide uppercase">OpenRecruitment DOSCOM</p>
            <h1 class="text-2xl font-bold tracking-tight">Pantau pendaftaran</h1>
            <p class="text-muted-foreground text-sm">
                Masukkan nomor pendaftaran dan token dari email konfirmasi.
            </p>
        </div>

        <Card class="rounded-2xl border-border/70">
            <CardHeader>
                <CardTitle class="text-lg">Masuk tracking</CardTitle>
            </CardHeader>
            <CardContent>
                <form class="space-y-4" @submit.prevent="submit">
                    <p v-if="form.errors.credentials" class="text-destructive text-sm">
                        {{ form.errors.credentials }}
                    </p>
                    <p v-if="form.errors.tracking" class="text-destructive text-sm">
                        {{ form.errors.tracking }}
                    </p>

                    <div class="space-y-2">
                        <Label for="registration_number">Nomor pendaftaran</Label>
                        <Input
                            id="registration_number"
                            v-model="form.registration_number"
                            placeholder="OPREC-2026-00001"
                            autocomplete="off"
                            required
                        />
                        <p v-if="form.errors.registration_number" class="text-destructive text-xs">
                            {{ form.errors.registration_number }}
                        </p>
                    </div>

                    <div class="space-y-2">
                        <Label for="tracking_token">Token tracking</Label>
                        <Input
                            id="tracking_token"
                            v-model="form.tracking_token"
                            type="password"
                            autocomplete="off"
                            required
                        />
                        <p v-if="form.errors.tracking_token" class="text-destructive text-xs">
                            {{ form.errors.tracking_token }}
                        </p>
                    </div>

                    <div class="flex flex-wrap gap-3 pt-2">
                        <Button type="submit" :disabled="form.processing">
                            {{ form.processing ? 'Memverifikasi...' : 'Lihat progress' }}
                        </Button>
                        <Button as-child variant="outline" type="button">
                            <Link :href="routes.openRecruitment.landing">Kembali</Link>
                        </Button>
                    </div>
                </form>
            </CardContent>
        </Card>
    </div>
</template>
