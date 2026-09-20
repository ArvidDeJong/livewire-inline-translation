# Security policy

This package lets an authorised user change the text a site shows. A way for someone without that authorisation to change it, or to get script into the page through it, counts as a security issue.

## Supported versions

Only the latest minor release of 1.x receives security fixes. Upgrade before reporting.

## Reporting a vulnerability

Please do **not** open a public issue. Report it privately instead:

- via [GitHub private vulnerability reporting](https://github.com/ArvidDeJong/livewire-inline-translation/security/advisories/new), or
- by email to info@arvid.nl.

Include the package version, the Laravel and Livewire versions, your `guard` setting, and the steps that let an unauthorised visitor read or change a translation.

You will get a reply within a week. Once a fix is released, the advisory is published and you are credited, unless you prefer not to be.

## Known and intended behaviour

These are design choices, documented in [How it works](https://arviddejong.github.io/livewire-inline-translation/how-it-works.html), not vulnerabilities:

- **The stored value is rendered as HTML.** That is the point of the HTML editor mode, and it means whoever may edit a translation may put script on the page. Only give the configured guard to people you would also give that. Sanitise in your own `save()` if you want to narrow it.
- **Authorisation is one guard check.** The component asks `Auth::guard($guard)->check()`, nothing more. If you need per user or per key permissions, wrap the component in your own and gate it there.
- **Every visitor sees the current translation.** The component reads the database value for everyone; only the edit affordance and the modal are behind the guard.
