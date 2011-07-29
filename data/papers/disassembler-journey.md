A story about writing a disassembler
====================================
I always wanted to write a disassembler (and possibly an assembler and a compiler), few years ago
I was tempted to do it in C, and wasted various days designing it basing it off blacklight's code
from ASMash.

The results were crappy, a lot, I wasn't experienced as I am now, so I just throw it away and stopped
caring.

Now I'm a lot more experienced in a lot more languages and subjects and it's the right time to do
what I wanted to, but this time in Ruby.

Already rewrote the design twice, going for the third and theoretically last rewrite (as usual three
rewrites are required to get to a good shape)

Few days later
--------------
Today a new discovery, Intel engineers are smart motherfuckers. In 8086 `0x0F, 0x60 .. 0x6F, 0xC0, 0xC1,0xC9, 0xD6, 0xF1`
these opcodes are holes, yeah, they're like nops, they don't do a thing.

In IA32 instead 0x66 and 0x67 are used when choosing if the instruction is working in 16 bit or in 32 bit, this means
that IA32 **IS** completely backward compatible with 8086, magic.
