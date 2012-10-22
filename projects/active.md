---
title: meh, active projects
lang: en

layout: projects
---

[barlume](https://github.com/meh/barlume)
-----------------------------------------
barlume is an attempt at creating a fully-Ruby library to easyly handle
asynchronous I/O.

It wraps various selecting primitives with ffi and makes them accessible with a
simple shared interface.

It also provides a nice reactor/proactor machine with an EventMachine
compatibility layer which makes it easy to use already existing libraries for
EventMachine while using a better backend.

This means that you can also run your EventMachine programs under barlume by
just replacing `require 'eventmachine'` with `require 'barlume/em'.

<hr/>

[nucular](https://github.com/meh/nucular)
-----------------------------------------
nucular is a reactor fully written in D that is greatly inspired by
EventMachine.

It already works and can be used to implement any kind of protocol. Basic
protocols are already provided and implement in `nucular/protocols`, you can
use them as complex examples to how implementing other protocols.

What I want from nucular is expanding D usage and provide a simple interface to
write network programs and libraries.

Keep in mind this is my first D project so it will have some ugly parts, don't
hesitate to point them out, I'll gladly fix them to be more consistent with D
standards and suggested usages.

The primitives are implemented directly in D instead of using *libev* or
similar.

<hr/>

[torrone](https://github.com/torrone)
-------------------------------------
torrone is an anonymous, secure and resilient tool to create self-managed
communities providing a forum, group chat, voice chat, file sharing and other
goods.
