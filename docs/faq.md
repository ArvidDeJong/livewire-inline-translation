---
title: FAQ
nav_order: 7
description: "Short answers about darvis/livewire-inline-translation: who may edit, where the text is stored, and what happens to your language files."
faq: true
---

# Frequently asked questions

{% for item in site.data.faq %}
## {{ item.q }}

{{ item.a | markdownify }}
{% endfor %}
