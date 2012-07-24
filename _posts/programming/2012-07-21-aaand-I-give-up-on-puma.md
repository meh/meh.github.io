---
title: Puma and the baww
categories: [programming, ruby]
layout: post
---

My try at writing a patch for Puma has gone pretty badly, bugs that didn't make sense at all,
I think there's some crucial issue behind the scenes that I didn't notice so I get weird behavior
and the like.

So yeah, I've got better things to do, at least it gave me some ideas for barlume and made
me find few bugs.

Next train: reactor and proactor land.

EDIT: with the last fix including barlume in puma seems easy, too bad Rubinius lacks `FFI::Union`
and `FFI::Struct#pack`, I still give up for the moment, until someone adds that to Rubinius.

EDIT2: and that someone could be me, Rubinius really needs an update to compatibility with the
ffi gem.
