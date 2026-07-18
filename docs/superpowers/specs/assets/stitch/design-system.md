---
name: FocusFlow
colors:
  surface: '#f8f9fa'
  surface-dim: '#d9dadb'
  surface-bright: '#f8f9fa'
  surface-container-lowest: '#ffffff'
  surface-container-low: '#f3f4f5'
  surface-container: '#edeeef'
  surface-container-high: '#e7e8e9'
  surface-container-highest: '#e1e3e4'
  on-surface: '#191c1d'
  on-surface-variant: '#414844'
  inverse-surface: '#2e3132'
  inverse-on-surface: '#f0f1f2'
  outline: '#717973'
  outline-variant: '#c1c8c2'
  surface-tint: '#3f6653'
  primary: '#012d1d'
  on-primary: '#ffffff'
  primary-container: '#1b4332'
  on-primary-container: '#86af99'
  inverse-primary: '#a5d0b9'
  secondary: '#0e6c4a'
  on-secondary: '#ffffff'
  secondary-container: '#a0f4c8'
  on-secondary-container: '#19724f'
  tertiary: '#152b1c'
  on-tertiary: '#ffffff'
  tertiary-container: '#2a4131'
  on-tertiary-container: '#93ad98'
  error: '#ba1a1a'
  on-error: '#ffffff'
  error-container: '#ffdad6'
  on-error-container: '#93000a'
  primary-fixed: '#c1ecd4'
  primary-fixed-dim: '#a5d0b9'
  on-primary-fixed: '#002114'
  on-primary-fixed-variant: '#274e3d'
  secondary-fixed: '#a0f4c8'
  secondary-fixed-dim: '#85d7ad'
  on-secondary-fixed: '#002113'
  on-secondary-fixed-variant: '#005236'
  tertiary-fixed: '#cee9d3'
  tertiary-fixed-dim: '#b3cdb7'
  on-tertiary-fixed: '#092012'
  on-tertiary-fixed-variant: '#354c3b'
  background: '#f8f9fa'
  on-background: '#191c1d'
  surface-variant: '#e1e3e4'
typography:
  headline-lg:
    fontFamily: Manrope
    fontSize: 40px
    fontWeight: '700'
    lineHeight: 48px
    letterSpacing: -0.02em
  headline-lg-mobile:
    fontFamily: Manrope
    fontSize: 30px
    fontWeight: '700'
    lineHeight: 36px
    letterSpacing: -0.02em
  headline-md:
    fontFamily: Manrope
    fontSize: 28px
    fontWeight: '600'
    lineHeight: 34px
    letterSpacing: -0.01em
  headline-sm:
    fontFamily: Manrope
    fontSize: 20px
    fontWeight: '600'
    lineHeight: 28px
    letterSpacing: '0'
  body-lg:
    fontFamily: Inter
    fontSize: 18px
    fontWeight: '400'
    lineHeight: 28px
  body-md:
    fontFamily: Inter
    fontSize: 16px
    fontWeight: '400'
    lineHeight: 24px
  body-sm:
    fontFamily: Inter
    fontSize: 14px
    fontWeight: '400'
    lineHeight: 20px
  label-md:
    fontFamily: JetBrains Mono
    fontSize: 14px
    fontWeight: '500'
    lineHeight: 16px
    letterSpacing: 0.05em
  label-sm:
    fontFamily: JetBrains Mono
    fontSize: 12px
    fontWeight: '500'
    lineHeight: 14px
    letterSpacing: 0.05em
rounded:
  sm: 0.25rem
  DEFAULT: 0.5rem
  md: 0.75rem
  lg: 1rem
  xl: 1.5rem
  full: 9999px
spacing:
  unit: 4px
  xs: 4px
  sm: 8px
  md: 16px
  lg: 24px
  xl: 40px
  gutter: 24px
  margin-mobile: 16px
  margin-desktop: 64px
---

## Brand & Style

This design system is built on a philosophy of **Organic Minimalism**. The brand personality is grounded, tranquil, and hyper-efficient, aiming to reduce cognitive load for users managing complex workflows. The aesthetic draws from "Biophilic Design," bringing the calming influence of nature into a digital productivity space.

The UI avoids the sterile coldness of traditional enterprise software by using soft textures, natural color transitions, and generous white space. The target audience includes creative professionals and knowledge workers who seek a "deep work" environment that feels like a quiet, sunlit office rather than a crowded dashboard.

## Colors

The palette is anchored in a verdant spectrum to promote focus and mental clarity.

- **Primary (Forest Green):** Used for high-priority actions, active states, and primary navigation. It provides the "weight" needed for authority.
- **Secondary (Mint/Sage):** Used for progress indicators, secondary buttons, and success states. It adds a fresh, energetic lift to the UI.
- **Tertiary (Pale Mist):** Specifically for large surface areas, background subtle highlights, and soft borders.
- **Neutral:** A range of cool grays and crisp whites that ensure the interface remains airy and readable. Text is rendered in a very dark charcoal (#212529) rather than pure black to maintain the organic feel.

## Typography

The typography strategy balances modern efficiency with approachable warmth. 

- **Headlines (Manrope):** Chosen for its geometric but friendly structure. It feels professional yet modern.
- **Body (Inter):** The workhorse of the system, selected for its exceptional legibility in data-heavy productivity views.
- **Labels (JetBrains Mono):** Used sparingly for metadata, timestamps, and "Focus Session" timers to provide a subtle technical, precise edge to the organic aesthetic.

All text should maintain a high contrast ratio against the light backgrounds, prioritizing accessibility without sacrificing the soft aesthetic.

## Layout & Spacing

The layout utilizes a **fluid 12-column grid** on desktop, transitioning to a **4-column grid** on mobile. The spacing rhythm is based on a 4px baseline, but defaults to 16px (md) for most component spacing to ensure the interface feels "breathable."

- **Desktop:** Wide margins (64px) to center the user's focus on the main content area.
- **Tablets:** Adaptive gutters that shrink to 16px to maximize screen real estate.
- **Focus Mode:** A specific layout state where the sidebar and secondary navigation fade out or collapse, leaving a single-column content view with 800px max-width to eliminate distractions.

## Elevation & Depth

To maintain the organic feel, this design system avoids heavy shadows. Instead, it uses **Tonal Layers** and **Soft Ambient Occlusion**.

- **Level 0 (Background):** The base layer using the Neutral color.
- **Level 1 (Cards/Containers):** Pure white surfaces with a subtle 1px border in Tertiary (Sage) or a very soft, diffused 5% opacity Forest Green shadow.
- **Level 2 (Modals/Popovers):** Higher elevation achieved with a 12% opacity shadow and a backdrop blur (Glassmorphism) of 10px to keep the user grounded in their current context.

Depth is communicated more through color contrast and subtle borders than through physical height.

## Shapes

The shape language is consistently **Rounded**, reflecting the soft, organic nature of the brand. 

- **Buttons & Inputs:** Use the standard `rounded` (0.5rem) setting.
- **Cards & Sections:** Use `rounded-lg` (1rem) to create a gentle container for tasks and data.
- **Status Pills:** Use `rounded-xl` (1.5rem) or fully pill-shaped to differentiate them from actionable buttons.

Avoid sharp 90-degree corners entirely to prevent the UI from feeling "aggressive" or overly corporate.

## Components

### Buttons
- **Primary:** Solid Forest Green with White text. No gradients.
- **Secondary:** Ghost style with a Mint border and Forest Green text.
- **Actionable Icons:** Placed within Sage-tinted circular backgrounds for a soft touch.

### Input Fields
Inputs use a subtle Pale Mist background that shifts to White on focus, with a Forest Green 2px bottom-border or full-border highlight to indicate activity.

### Chips & Tags
Used for categorization. They should use the Tertiary (Sage) background with Forest Green text to ensure they are legible but don't compete with primary actions.

### Progress Bars
Utilize a soft Mint for the track and a vibrant Forest Green for the fill. The motion should be eased (cubic-bezier) to feel natural.

### Cards
Cards are the primary organizational unit. They feature 1rem corner rounding, a 1px Sage border, and no shadow unless hovered. On hover, a card should subtly lift with a soft shadow and a 2px Forest Green left-accent bar to show focus.