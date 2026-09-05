<script setup lang="ts">
import { ref, watch } from 'vue'
import { Info } from 'lucide-vue-next'
import TipTapEditor from '@/components/modules/dashboard/events/TipTapEditor.vue'

const successContent = defineModel<string>('successContent', { required: true })
const successEnabled = defineModel<boolean>('successEnabled', { required: true })

function hasMeaningfulContent(html: string): boolean {
    if (!html) return false
    const text = html.replace(/<[^>]*>/g, '').replace(/&nbsp;/gi, ' ').trim()
    return text !== ''
}

/**
 * Editor TipTap hanya dipasang saat `successEnabled` ON → unmount saat OFF
 * sehingga tidak ada konten basi. `successContent` dikosongkan oleh toggle
 * di composable (Workspace) saat OFF.
 */
const editorMounted = ref(successEnabled.value && hasMeaningfulContent(successContent.value))

watch(
    () => successEnabled.value,
    (on) => {
        editorMounted.value = on
        // OFF dari panel kiri → pastikan tidak ada sisa HTML.
        if (!on && successContent.value !== '') {
            successContent.value = ''
        }
    },
)

// Jaga konsistensi dua arah antara toggle & isi editor:
// - konten terhapus semua saat ON → matikan toggle + bersihkan HTML.
// - konten bermakna hadir dari luar (mis. data tersimpan dimuat belakangan) → nyalakan toggle.
watch(
    () => successContent.value,
    (v) => {
        const meaningful = hasMeaningfulContent(v)
        if (successEnabled.value && !meaningful) {
            successEnabled.value = false
            if (v !== '') successContent.value = ''
        } else if (!successEnabled.value && meaningful) {
            successEnabled.value = true
        }
    },
)
</script>

<template>
    <div>
        <div class="border-border/70 flex items-center gap-2 border-b px-5 py-3.5 sm:px-7">
            <Info class="text-muted-foreground size-4" aria-hidden="true" />
            <h2 class="text-foreground text-sm font-semibold tracking-[-0.01em]">Pesan setelah submit</h2>
        </div>

        <div class="px-4 py-4 sm:px-5 sm:py-5">
            <TipTapEditor v-if="editorMounted && successEnabled" v-model="successContent" />
        </div>
    </div>
</template>
