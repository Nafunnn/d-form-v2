/**
 * Menghapus tag HTML dan memadatkan spasi — dipakai untuk meta description / JSON-LD teks.
 */
export function stripHtmlToText(html: string | undefined | null, maxLength = 320): string {
    if (html == null || html === '') {
        return '';
    }
    let text = html
        .replace(/<script[\s\S]*?<\/script>/gi, ' ')
        .replace(/<style[\s\S]*?<\/style>/gi, ' ')
        .replace(/<[^>]+>/g, ' ')
        .replace(/\s+/g, ' ')
        .trim();

    if (text.length > maxLength) {
        text = text.slice(0, Math.max(0, maxLength - 1)).trimEnd() + '…';
    }

    return text;
}
