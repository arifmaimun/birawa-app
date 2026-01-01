# Responsive Design Guide - Birawa Vet

This guide outlines the responsive design system and standards implemented in the Birawa Vet application to ensure a seamless experience across mobile, tablet, and desktop devices.

## 1. Breakpoints System

We use Tailwind CSS default breakpoints, following a **Mobile-First** approach.

| Breakpoint | Width (min) | Device Category      | Layout Behavior                                                                 |
|------------|-------------|----------------------|---------------------------------------------------------------------------------|
| **Default**| 0px         | Mobile (Portrait)    | Single column, Bottom Navigation, Full-width cards                              |
| **sm**     | 640px       | Mobile (Landscape)   | Grid starts to split (2 cols), larger padding                                   |
| **md**     | 768px       | Tablet / Small Laptop| **Major Switch**: Bottom Nav -> Left Sidebar. Content offset (`ml-64`).         |
| **lg**     | 1024px      | Laptop / Desktop     | Multi-column grids (3+ cols), comfortable spacing                               |
| **xl**     | 1280px      | Large Desktop        | Max widths apply (`max-w-7xl`), centered content                                |

## 2. Core Layout Patterns

### Navigation Adaptation
- **Mobile (< 768px)**:
  - Fixed **Bottom Navigation Bar** (`fixed bottom-0`).
  - Height: `h-20` (80px).
  - Main content padding: `pb-safe` (safe area for iPhone home bar).
  - Menus: "More" menu opens as a bottom sheet/popover.

- **Desktop (>= 768px)**:
  - Fixed **Left Sidebar** (`fixed left-0 top-16`).
  - Width: `w-64` (256px).
  - Main content margin: `md:ml-64`.
  - Header/Top bar adapts to full width minus sidebar.

### Content Layouts
- **Grids**:
  - Default: `grid-cols-1` (Mobile)
  - Tablet: `sm:grid-cols-2`
  - Desktop: `lg:grid-cols-3` or `grid-cols-4`
- **Tables/Lists**:
  - Mobile: Card views (stacked info) or horizontally scrollable tables.
  - Desktop: Full data tables.

## 3. Component Standards

### Typography & Units
- Use **rem** for font sizes and spacing to respect user browser settings.
- **Minimum Font Size**: `text-xs` (12px) for labels, `text-sm` (14px) or `text-base` (16px) for body text.
- **Inputs**: Ensure font size is at least 16px on mobile to prevent iOS auto-zoom.

### Touch Targets
- Interactive elements (buttons, links, icons) must have a minimum hit area of **48x48px**.
- Use padding to increase hit area without increasing visual size if necessary.

### Fixed Elements
- **Z-Index**:
  - Bottom Navigation: `z-50`
  - Modals/Overlays: `z-50` +
  - Fixed Action Buttons: Ensure they do not overlap navigation. Use `bottom-20` on mobile if bottom nav is present.

## 4. Performance & Stability
- **CLS (Cumulative Layout Shift)**: Always define `aspect-ratio` or fixed dimensions for images.
- **Transitions**: Use `duration-300 ease-in-out` for smooth layout changes (e.g., sidebar appearing).
- **Will-Change**: Use sparingly on animating fixed elements like sidebars.

## 5. Implementation Example

**Responsive Grid Card:**

```html
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
    <!-- Card Item -->
    <div class="bg-white p-4 rounded-xl shadow-sm">
        <!-- Content -->
    </div>
</div>
```

**Responsive Visibility:**

```html
<!-- Visible only on Mobile -->
<div class="md:hidden">...</div>

<!-- Visible only on Desktop -->
<div class="hidden md:block">...</div>
```

## 6. Testing Checklist
- [ ] Check layout on 320px width (iPhone SE).
- [ ] Check layout on 768px (iPad Portrait) - Ensure Sidebar activates.
- [ ] Verify no horizontal scroll on mobile.
- [ ] Ensure touch targets are clickable without zooming.
- [ ] Test text scaling (Accessibility).
