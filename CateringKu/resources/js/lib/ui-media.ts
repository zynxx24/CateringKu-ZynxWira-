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

export function getVendorPlaceholder(name: string) {
    const initial = escapeText((name || 'C').trim().charAt(0).toUpperCase() || 'C')
    const label = escapeText(name || 'CateringKu')

    return svgToDataUri(`
        <svg width="1200" height="800" viewBox="0 0 1200 800" fill="none" xmlns="http://www.w3.org/2000/svg">
            <rect width="1200" height="800" rx="48" fill="#FFF6EE"/>
            <rect width="1200" height="800" fill="url(#bg)"/>
            <circle cx="988" cy="112" r="196" fill="#FFFFFF" fill-opacity="0.18"/>
            <circle cx="194" cy="696" r="174" fill="#FFFFFF" fill-opacity="0.12"/>
            <rect x="92" y="110" width="180" height="180" rx="36" fill="#FFFFFF" fill-opacity="0.22"/>
            <text x="182" y="228" text-anchor="middle" font-family="Arial, Helvetica, sans-serif" font-size="108" font-weight="700" fill="#FFFFFF">${initial}</text>
            <text x="92" y="382" font-family="Arial, Helvetica, sans-serif" font-size="64" font-weight="700" fill="#FFFFFF">${label}</text>
            <text x="92" y="444" font-family="Arial, Helvetica, sans-serif" font-size="30" fill="#FFF2E8">Vendor katering terpercaya</text>
            <defs>
                <linearGradient id="bg" x1="92" y1="56" x2="1030" y2="760" gradientUnits="userSpaceOnUse">
                    <stop stop-color="#F97316"/>
                    <stop offset="1" stop-color="#EA580C"/>
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
            <text x="400" y="512" text-anchor="middle" font-family="Arial, Helvetica, sans-serif" font-size="24" fill="#C2410C">Siap disajikan untuk acara Anda</text>
            <defs>
                <linearGradient id="bg" x1="58" y1="44" x2="740" y2="566" gradientUnits="userSpaceOnUse">
                    <stop stop-color="#FFEDD5"/>
                    <stop offset="1" stop-color="#FED7AA"/>
                </linearGradient>
            </defs>
        </svg>
    `)
}
