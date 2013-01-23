---
title: Arrays of associative arrays in Zsh
categories: [programming, zsh]
layout: post
---

While implementing [zwm][1] I hit a small wall, the setup packet of the X
protocol contains an array of structs, specifically a list of `FORMAT` and a
list of `SCREEN`.

Zsh doesn't support setting arrays or associative arrays into other arrays or
associative arrays, so you're stuck with a single level.

The first thought I had was to set a string with the content of the associative
array (which emulates the struct) and then eval it, but that would be kinda
slow and most of all, ugly as fuck.

Soon enough enlightenment struck me.

We can set an associative array with a numbered serie of key names, like
follows:

```bash
local -A map
map=(roots.length 2 roots.1.window 23 roots.2.window 42)
```

We start from 1 as index because that's what Zsh uses for indexed arrays.

Now that we have our array of *associative arrays*, we can loop over them and
access them more easily:

```bash
integer i
for (( i=1; i <= $map[roots.length]; ++i )); do
  local -A root
  root=("${(@kv)map[(I)roots.$i.*]#roots.$i.}")

  print "root window for root number $i: $root[window]"
done
```

Ta dah.

[1]: https://github.com/meh/zwm
