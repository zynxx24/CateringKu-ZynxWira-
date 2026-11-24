function svgToDataUri(svg: string) {
    return `data:image/svg+xml;charset=UTF-8,${encodeURIComponent(svg)}`
}

function escapeText(value: string) {
    return value
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;')
}

/**
 * Stand-in picture for a vendor that has not uploaded a logo: a monogram, nothing more.
 * The name already appears under the image, and the card must not claim anything about the vendor.
 */
export function getVendorPlaceholder(name: string) {
    const initial = escapeText((name || 'C').trim().charAt(0).toUpperCase() || 'C')

    return svgToDataUri(`
        <svg width="1200" height="800" viewBox="0 0 1200 800" fill="none" xmlns="http://www.w3.org/2000/svg">
            <rect width="1200" height="800" fill="url(#bg)"/>
            <circle cx="988" cy="112" r="196" fill="#FFFFFF" fill-opacity="0.08"/>
            <circle cx="194" cy="696" r="174" fill="#FFFFFF" fill-opacity="0.06"/>
            <text x="600" y="470" text-anchor="middle" font-family="Poppins, Arial, Helvetica, sans-serif" font-size="260" font-weight="700" fill="#FFFFFF">${initial}</text>
            <defs>
                <linearGradient id="bg" x1="92" y1="56" x2="1030" y2="760" gradientUnits="userSpaceOnUse">
                    <stop stop-color="#B8461F"/>
                    <stop offset="1" stop-color="#9A3412"/>
                </linearGradient>
            </defs>
        </svg>
    `)
}

export function getMenuPlaceholder(name: string) {
    const label = escapeText(name || 'Menu pilihan')

    return svgToDataUri(`
        <svg width="800" height="600" viewBox="0 0 800 600" fill="none" xmlns="http://www.w3.org/2000/svg">
            <rect width="800" height="600" rx="36" fill="#FFF7ED"/>
            <rect width="800" height="600" fill="url(#bg)"/>
            <circle cx="657" cy="110" r="126" fill="#FFFFFF" fill-opacity="0.2"/>
            <circle cx="138" cy="528" r="96" fill="#FFFFFF" fill-opacity="0.15"/>
            <circle cx="400" cy="256" r="130" fill="#FFFFFF" fill-opacity="0.95"/>
            <circle cx="400" cy="256" r="98" stroke="#FDBA74" stroke-width="18"/>
            <path d="M348 228C365 205 387 194 415 194C459 194 492 233 492 281" stroke="#EA580C" stroke-width="20" stroke-linecap="round"/>
            <path d="M314 318H486" stroke="#EA580C" stroke-width="22" stroke-linecap="round"/>
            <path d="M342 354H458" stroke="#FDBA74" stroke-width="18" stroke-linecap="round"/>
            <text x="400" y="468" text-anchor="middle" font-family="Arial, Helvetica, sans-serif" font-size="38" font-weight="700" fill="#9A3412">${label}</text>
            <defs>
                <linearGradient id="bg" x1="58" y1="44" x2="740" y2="566" gradientUnits="userSpaceOnUse">
                    <stop stop-color="#FFEDD5"/>
                    <stop offset="1" stop-color="#FED7AA"/>
                </linearGradient>
            </defs>
        </svg>
    `)
}
