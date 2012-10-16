---
title: Why Node.js is the Apple of the programming world
categories: [programming, sadness]
layout: post
---

I am sadly in contact with a guy that has only ever worked with Node.js and
Javascript in general, and sometimes his ignorance and *ah-ha!* moments make me
cringe myself to death.

I'm not implying that one guy says anything meaningful about the rest of people
using Node.js, but it surely makes me think about how Node.js handles things
and the perception most people have of it.

They make everything look magical, some people seriously think Node.js invented
the reactor pattern, and that they singlehandedly made some revolutionary
technological breakthrough with it.

Going back to that guy, he was talking about the Node.js *streams* like they
were some kind of legendary creature, because *"you can read chunks!"*.

What caused this post was his last question, he asked if it was possible to
know how many bytes you're sending the server without having access to the
server to communicate the amount of received bytes.

His rationalization was *"wget does it, so I guess you can"*.

This made me sad inside, how many people writing web and network services don't
even know what sockets are and how they work?

*Mother forgive me.*
