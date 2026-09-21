---
title: "FAQ"
nav_order: 9
description: "Short answers about darvis/livewire-inline-translation: what it is, which versions it supports, who may edit, where the text is stored and whether it is safe."
faq: true
---

# Frequently asked questions

{% for item in site.data.faq %}
## {{ item.q }}

{{ item.a | markdownify }}
{% endfor %}
