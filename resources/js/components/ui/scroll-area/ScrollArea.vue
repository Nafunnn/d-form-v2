<script setup lang="ts">
import type { HTMLAttributes } from 'vue'
import { ScrollAreaRoot, ScrollAreaScrollbar, ScrollAreaThumb, ScrollAreaViewport } from 'reka-ui'
import { cn } from '@/lib/utils'

const props = defineProps<{ class?: HTMLAttributes['class'] }>()
</script>

<template>
    <ScrollAreaRoot :class="cn('relative overflow-hidden', props.class)">
        <ScrollAreaViewport class="h-full w-full rounded-[inherit]">
            <slot />
        </ScrollAreaViewport>
        <ScrollAreaScrollbar
            class="flex touch-none select-none p-px transition-opacity data-[orientation=horizontal]:h-2.5 data-[orientation=horizontal]:flex-col data-[orientation=horizontal]:border-t data-[orientation=horizontal]:border-t-transparent data-[orientation=vertical]:h-full data-[orientation=vertical]:w-2.5 data-[orientation=vertical]:border-l data-[orientation=vertical]:border-l-transparent"
            orientation="vertical"
        >
            <ScrollAreaThumb class="app-scroll-area-thumb relative flex-1 rounded-full" />
        </ScrollAreaScrollbar>
        <ScrollAreaScrollbar
            class="flex touch-none select-none p-px transition-opacity data-[orientation=horizontal]:h-2.5 data-[orientation=horizontal]:flex-col data-[orientation=horizontal]:border-t data-[orientation=horizontal]:border-t-transparent data-[orientation=vertical]:h-full data-[orientation=vertical]:w-2.5 data-[orientation=vertical]:border-l data-[orientation=vertical]:border-l-transparent"
            orientation="horizontal"
        >
            <ScrollAreaThumb class="app-scroll-area-thumb relative flex-1 rounded-full" />
        </ScrollAreaScrollbar>
    </ScrollAreaRoot>
</template>
