<?php
/** Decorative SVG scenery (no external images needed). */

function hero_art(): string
{
    return <<<'SVG'
<svg class="hero-art" viewBox="0 0 1200 560" preserveAspectRatio="xMidYMid slice" aria-hidden="true">
  <defs>
    <linearGradient id="sky" x1="0" y1="0" x2="0" y2="1">
      <stop offset="0" stop-color="#f3e3c4"/><stop offset=".45" stop-color="#d8c4a8"/><stop offset="1" stop-color="#A78D78"/>
    </linearGradient>
    <radialGradient id="sun" cx=".5" cy=".5" r=".5">
      <stop offset="0" stop-color="#fff8e8"/><stop offset=".35" stop-color="#f8e6c0" stop-opacity=".85"/><stop offset="1" stop-color="#f3e3c4" stop-opacity="0"/>
    </radialGradient>
    <linearGradient id="grass" x1="0" y1="0" x2="0" y2="1">
      <stop offset="0" stop-color="#8d6a58"/><stop offset="1" stop-color="#4a2f24"/>
    </linearGradient>
    <linearGradient id="shade" x1="0" y1="0" x2="1" y2="0">
      <stop offset="0" stop-color="#1a0f06" stop-opacity=".62"/><stop offset=".55" stop-color="#1a0f06" stop-opacity=".12"/><stop offset="1" stop-color="#1a0f06" stop-opacity="0"/>
    </linearGradient>
  </defs>
  <rect width="1200" height="560" fill="url(#sky)"/>
  <circle cx="640" cy="170" r="230" fill="url(#sun)"/>
  <path d="M0 330 L170 230 L300 300 L470 190 L640 300 L790 220 L960 310 L1090 240 L1200 300 V430 H0Z" fill="#8d7261" opacity=".6"/>
  <path d="M0 380 Q200 300 420 370 T820 360 T1200 350 V470 H0Z" fill="#6E473B"/>
  <path d="M0 430 Q260 380 520 430 T1200 420 V560 H0Z" fill="url(#grass)"/>
  <!-- pines (right) -->
  <g fill="#33200f">
    <path d="M960 120 l50 110 h-34 l44 80 h-40 l52 90 h-144 l52 -90 h-40 l44 -80 h-34z" transform="translate(40 40) scale(.9)"/>
    <path d="M1110 170 l38 84 h-26 l34 60 h-30 l40 70 h-112 l40 -70 h-30 l34 -60 h-26z" transform="translate(-10 20) scale(.8)"/>
  </g>
  <!-- cabin -->
  <g transform="translate(700 255)">
    <rect x="30" y="70" width="270" height="110" rx="4" fill="#E1D4C2"/>
    <rect x="30" y="70" width="270" height="14" fill="#BEB5A9"/>
    <path d="M0 80 L165 8 L330 80 L316 96 L165 32 L14 96Z" fill="#6E473B"/>
    <path d="M165 8 L330 80 L316 96 L165 32Z" fill="#4a2f24"/>
    <rect x="212" y="98" width="62" height="64" rx="4" fill="#ffd9a0"/>
    <rect x="212" y="98" width="62" height="64" rx="4" fill="none" stroke="#291C0E" stroke-width="5"/>
    <path d="M243 98 v64 M212 130 h62" stroke="#291C0E" stroke-width="4"/>
    <rect x="84" y="108" width="56" height="72" rx="4" fill="#291C0E"/>
    <rect x="22" y="176" width="286" height="12" rx="6" fill="#A78D78"/>
    <rect x="236" y="-8" width="28" height="44" fill="#8d6a58"/><rect x="230" y="-14" width="40" height="10" fill="#BEB5A9"/>
  </g>
  <!-- big tree (left) -->
  <g>
    <path d="M40 560 C60 380 30 250 70 60 H120 C100 250 130 380 120 560Z" fill="#291C0E"/>
    <ellipse cx="90" cy="90" rx="190" ry="110" fill="#4a2f24"/>
    <ellipse cx="250" cy="70" rx="140" ry="70" fill="#6E473B"/>
    <ellipse cx="10" cy="200" rx="110" ry="80" fill="#33200f"/>
  </g>
  <g fill="#3a2616"><ellipse cx="470" cy="450" rx="46" ry="26"/><ellipse cx="1130" cy="470" rx="70" ry="42"/><ellipse cx="640" cy="468" rx="34" ry="18"/></g>
  <rect width="1200" height="560" fill="url(#shade)"/>
</svg>
SVG;
}

function cabin_art(): string
{
    return <<<'SVG'
<svg viewBox="0 0 420 300" preserveAspectRatio="xMidYMid slice" aria-hidden="true">
  <defs><linearGradient id="s2" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#E1D4C2"/><stop offset="1" stop-color="#F4EDE2"/></linearGradient></defs>
  <rect width="420" height="300" fill="url(#s2)"/>
  <g fill="#3a2616"><path d="M30 190 l34 -110 l34 110z"/><path d="M60 190 l30 -90 l30 90z" fill="#33200f"/><path d="M330 190 l40 -120 l40 120z"/></g>
  <rect y="210" width="420" height="90" fill="#A78D78"/>
  <g transform="translate(110 100)">
    <rect x="10" y="48" width="170" height="70" fill="#E1D4C2"/>
    <path d="M-6 54 L95 4 L196 54 L186 64 L95 22 L4 64Z" fill="#6E473B"/>
    <rect x="118" y="66" width="38" height="40" fill="#ffd9a0" stroke="#291C0E" stroke-width="4"/>
    <rect x="40" y="70" width="34" height="48" fill="#291C0E"/>
  </g>
</svg>
SVG;
}
