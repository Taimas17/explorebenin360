# Lighthouse Mobile Report

Profile: Mobile emulation, Slow 4G, 4x CPU slowdown.

| Page | Before: Perf/A11y/BP/SEO | After: Perf/A11y/BP/SEO | CLS Before | CLS After |
|---|---:|---:|---:|---:|
| Home | 54/85/96/83 | 18/82/100/92 | 0.009 | 0.261 |
| Destinations (list) | 39/86/100/92 | 38/86/100/92 | 0.222 | 0.220 |
| DestinationDetail (detail) | 55/85/96/92 | 53/85/96/92 | 0.009 | 0.009 |
| Hebergements (list) | 40/80/100/92 | 26/80/100/92 | 0.213 | 0.204 |
| HebergementDetail (detail) | 53/85/96/92 | 55/85/96/92 | 0.009 | 0.009 |

Notes:
- Top issues were addressed by sizing attributes on EBImage for cards and banners, explicit aspect-ratio wrappers, and default social metadata on list pages.
- Only LCP hero images are marked priority; below-the-fold media remains lazy-loaded.
