craftd, the high performance Minecraft server
=============================================

Finally with my blog back I can waste some good time writing to relax a bit :)

Let's talk a bit about craftd and Minecraft.

If you don't know what Minecraft is, [check it out](http://minecraft.net), if you know it
and you don't like it, you didn't try it at all. If you tried it and still don't like it,
you're a failure at life.

Preface given, the official Minecraft server is a real piece of... umh.. ham. Yeah, well,
the guys at Mojang aren't really good at networking, given the fact that their website is
more down than a school for trisomy 21 affected children, as [this post](http://corbinsimpson.com/entry/take-a-bow)
states, their server can't even handle properly their own protocol.

craftd in that situation would just be like "hey bro, talk to me again when there's 65k data in
your buffer kthx", in short that DoS would have no effect at all on performances, maybe just waste
a bit of memory, but in the end it would just throw away that data and keep going as nothing happened.

craftd is written in C (C99), object oriented, highly modular and event driven. It has a threaded worker
system where you can up/down the number of workers during runtime to adapt to the current workload.

If you think threads are bad, take your [ROFLscale](http://www.youtube.com/watch?v=majbJoD6fzo) somewhere else.

At this moment in development we have the core stuff up and running, few modules that make some stuff work and heavy
development going on. (We're also having a stack fuckup with &gt;30 players, but that's another story)
