import { ref, watch, type Ref } from 'vue';

export type AutosaveStatus = 'idle' | 'saving' | 'saved';

export interface AutosaveStorage {
    read(key: string): string | null;
    write(key: string, value: string): void;
    remove(key: string): void;
}

export interface UseAutosaveSyncOptions {
    debounceMs?: number;
    enabled?: Ref<boolean> | boolean;
    onError?: (message: string) => void;
    storage?: AutosaveStorage;
    storageKey?: string;
}

export interface UseAutosaveSyncResult {
    status: Ref<AutosaveStatus>;
    schedule: () => void;
    flush: () => Promise<void>;
    cancel: () => void;
}

/**
 * Auto-sync global: optimistik (UI berubah duluan) + debounce (remote belakangan).
 * Halaman menyuplai `source` (snapshot serial) dan `save` (cara menyimpan).
 */
export function useAutosaveSync(
    source: () => string,
    save: (snapshot: string) => Promise<void>,
    opts: UseAutosaveSyncOptions = {},
): UseAutosaveSyncResult {
    const debounceMs = opts.debounceMs ?? 800;
    const status = ref<AutosaveStatus>('idle');
    let debounceTimer: ReturnType<typeof setTimeout> | null = null;
    let saveSeq = 0;

    function isEnabled(): boolean {
        if (typeof opts.enabled === 'boolean') return opts.enabled;
        if (opts.enabled) return opts.enabled.value;
        return true;
    }

    function clearTimer(): void {
        if (debounceTimer) {
            clearTimeout(debounceTimer);
            debounceTimer = null;
        }
    }

    function schedule(): void {
        if (!isEnabled()) return;
        if (opts.storage && opts.storageKey) {
            opts.storage.write(opts.storageKey, source());
        }
        clearTimer();
        debounceTimer = setTimeout(() => {
            void flush();
        }, debounceMs);
    }

    async function flush(): Promise<void> {
        clearTimer();
        if (!isEnabled()) return;
        const seq = ++saveSeq;
        status.value = 'saving';
        try {
            await save(source());
            if (seq === saveSeq) status.value = 'saved';
        } catch (err) {
            if (seq === saveSeq) {
                status.value = 'idle';
                (opts.onError ?? (() => {}))(err instanceof Error ? err.message : 'Gagal menyimpan otomatis.');
            }
        }
    }

    function cancel(): void {
        clearTimer();
    }

    watch(source, () => {
        schedule();
    });

    return { status, schedule, flush, cancel };
}
