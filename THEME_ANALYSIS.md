# PAM Website Theme Analysis

## Current State Analysis (Feb 15, 2026)

### Colors Currently in Use (Hardcoded in Templates)

#### Primary Colors
- **Blue-900** (`#1e3a8a`) - Used extensively for:
  - Main headings (h1, h2)
  - Navigation links
  - Logo text
  - Section titles
  
#### Secondary/Accent Colors
- **Red-600** (`#dc2626`) - Used for:
  - Active navigation states
  - Hover effects
  - CTA button accents
  - Feature checkmarks (red-500)
  - Link hover states

#### Supporting Colors
- **Blue-600** (`#2563eb`) - Buttons, CTAs
- **Blue-700** (`#1d4ed8`) - Button hover states
- **Gray scales** - Text, backgrounds, borders

### Global Configuration Colors (content/globals/site.yaml)

These colors are defined but NOT currently used in templates:

```yaml
primary_color: '#1e40af'    # blue-800 (Tailwind equivalent)
secondary_color: '#f59e0b'  # amber-500 (Tailwind equivalent)
accent_color: '#059669'     # emerald-600 (Tailwind equivalent)
```

### Color Mapping to Tailwind Classes

| Hex Code | Tailwind Class | Current Usage | Config Name |
|----------|---------------|---------------|-------------|
| `#1e3a8a` | `blue-900` | Headers, nav, text | N/A (hardcoded) |
| `#dc2626` | `red-600` | Accents, hovers | N/A (hardcoded) |
| `#1e40af` | `blue-800` | None (defined in config) | primary_color |
| `#f59e0b` | `amber-500` | None (defined in config) | secondary_color |
| `#059669` | `emerald-600` | None (defined in config) | accent_color |

## Files Using Hardcoded Colors

### Critical Template Files
1. **Header** (`resources/views/partials/header.antlers.html`)
   - Line 7: `text-blue-900` (logo)
   - Line 20, 29, 37: `text-blue-900` (nav links)
   - Line 20, 37: `hover:text-red-600` (nav hover)
   - Line 43, 55, 58, 66, 73, 80: `text-blue-900` (mobile menu)

2. **Hero** (`resources/views/partials/hero.antlers.html`)
   - Line 7, 26: `text-blue-900` (main heading)
   - Line 23: `from-blue-50 to-red-50` (gradient background)

3. **Footer** (`resources/views/partials/footer.antlers.html`)
   - Line 65: `bg-blue-600 hover:bg-blue-700` (scroll to top button)

4. **CTA** (`resources/views/partials/cta.antlers.html`)
   - Line 15: `text-blue-700` (subtitle)
   - Line 19: `text-blue-900` (heading)
   - Line 32: `bg-blue-600 hover:bg-blue-700` (button)

5. **Featured Services** (`resources/views/partials/featured-services.antlers.html`)
   - Line 20: `text-blue-900` (heading)
   - Line 31: `bg-red-500` (checkmark background)
   - Line 45: `text-red-600 hover:text-red-700` (link)

6. **Link Button** (`resources/views/partials/link-button.antlers.html`)
   - Line 2: `bg-blue-900 hover:bg-blue-700` (button)

7. **Article** (`resources/views/partials/article.antlers.html`)
   - Line 17: `text-blue-900` (heading)

8. **About Page** (`resources/views/about.antlers.html`)
   - Line 14: `text-blue-900` (heading)

### CSS Files
- **site.css** (`resources/css/site.css`)
  - Line 47-48: `#dc2626` (red-600) for active nav links
  - Line 109: `#3b82f6` (blue-500) for form focus states
  - Line 116: `#ef4444` (red-500) for form errors

### Configuration Files
- **tailwind.config.js**
  - Line 25: `bg-red-600` (safelist)
  - Line 26: `border-red-500` (safelist)

## Design Elements Observed

### Layout Structure
1. **Navigation Bar**
   - Sticky header with white background
   - Logo on left, navigation links center/right
   - Dropdown menus for parent pages
   - Mobile hamburger menu
   - Blue-900 text with red-600 hover states

2. **Hero Section**
   - Two variants: main hero (with background image) and page hero (gradient)
   - Large heading (blue-900)
   - Descriptive text (gray-600)
   - Optional CTA buttons
   - Animated hero image (floating effect)

3. **Content Sections**
   - Alternating white/gray-50 backgrounds
   - Two-column grid layout (image + text)
   - Responsive order switching
   - Fade-in-up animations

4. **Footer**
   - Dark gray-900 background
   - White text
   - Company info, quick links
   - Blue-600 scroll-to-top button

### Design Patterns
- **Gradients**: `from-blue-50 to-red-50` (hero), linear gradients in forms
- **Shadows**: Hover effects with shadow transitions
- **Animations**: 
  - Fade-in-up on scroll
  - Float animation for hero images
  - Smooth transitions on hover
- **Typography**: Figtree font family
- **Spacing**: Consistent padding (py-16, py-20, py-24)

## Recommendations for Theme Unification

### Option 1: Use Global Config Colors (Recommended)
Update all templates to use the colors defined in `site.yaml`:
- Replace `blue-900` → `blue-800` (matches `#1e40af`)
- Replace `red-600` → `amber-500` (matches `#f59e0b`)
- Add accent color usage with `emerald-600` (matches `#059669`)

**Pros:**
- Centralized color management
- Easy to update theme from CMS
- Consistent with site configuration

**Cons:**
- Requires updating many template files
- May need to adjust color contrast/accessibility

### Option 2: Update Global Config to Match Current Design
Update `site.yaml` to match the current hardcoded colors:
```yaml
primary_color: '#1e3a8a'  # blue-900
secondary_color: '#dc2626'  # red-600
accent_color: '#2563eb'  # blue-600
```

**Pros:**
- Minimal template changes
- Preserves current visual design
- Quick implementation

**Cons:**
- Doesn't leverage existing config system
- Still requires making templates dynamic

### Option 3: Create Tailwind Theme Extension
Extend Tailwind config to use CSS custom properties from global config:
- Add custom color definitions in `tailwind.config.js`
- Use CSS variables for dynamic theming
- Update templates to use custom color names

**Pros:**
- Most flexible solution
- Enables runtime theme switching
- Best practice for maintainability

**Cons:**
- More complex implementation
- Requires Tailwind configuration changes

## Next Steps

1. **Decide on approach** (Option 1, 2, or 3)
2. **Create color mapping document** for consistency
3. **Update templates systematically**:
   - Start with partials (header, footer, hero)
   - Then update page templates
   - Finally update CSS files
4. **Test across all pages** for visual consistency
5. **Verify accessibility** (color contrast ratios)
6. **Document theme usage** for future developers

## Files Requiring Updates (for Option 1 or 2)

### High Priority
- [ ] `resources/views/partials/header.antlers.html`
- [ ] `resources/views/partials/hero.antlers.html`
- [ ] `resources/views/partials/footer.antlers.html`
- [ ] `resources/views/partials/link-button.antlers.html`
- [ ] `resources/views/partials/cta.antlers.html`
- [ ] `resources/css/site.css`

### Medium Priority
- [ ] `resources/views/partials/featured-services.antlers.html`
- [ ] `resources/views/partials/article.antlers.html`
- [ ] `resources/views/about.antlers.html`
- [ ] `resources/views/partials/core-strengths.antlers.html`
- [ ] `resources/views/partials/contact/contact-form.antlers.html`

### Low Priority
- [ ] Other page templates
- [ ] Email templates
- [ ] Form field templates

## Color Accessibility Notes

Current color combinations should be tested for WCAG compliance:
- Blue-900 on white: ✓ (High contrast)
- Red-600 on white: ✓ (High contrast)
- Blue-700 on white: ✓ (High contrast)
- Gray-600 on white: ✓ (Sufficient for body text)

If switching to amber-500 (`#f59e0b`), verify contrast ratios for text usage.
