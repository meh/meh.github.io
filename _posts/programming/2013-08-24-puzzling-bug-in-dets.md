---
title: Incomprehensible bug in DETS wasted hours of my time.
categories: [programming, erlang, elixir]
layout: post
---

I was playing with DETS and I kept getting weird `{ error, { bad_object_header,
* } }` without understanding what was going on.

The table got broken permanently after the fact, but only one process was
accessing the table so it wasn't a race.

Apparently if you use a tuple as key and you have differing shapes of it as key
the table gets broken permanently.

So if you use as key `{ 1, 2 }` and `{ 1, 2, 3 }` you'll get a broken table,
although you could use `{ 1, 2 }` and `{ 1, { 2, 3 } }`, and probably `\{{ 1, 2
}\}` and `\{{ 1, 2, 3 }\}`.

The more you know.
