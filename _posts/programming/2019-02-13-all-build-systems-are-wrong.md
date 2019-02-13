---
title: All build systems for C and C++ are wrong.
categories: [programming, c, cxx]
layout: post
---

Since I haven't blogged in years and I feel like starting again I thought it
would be a good idea to start with something negative that always irked me, the
state of build systems for C and C++.

Now don't get me wrong, over time things have been getting slightly better in
some regards, and much worse in others, but all in all things are still
terrible.

In this blog post I'm going to cover why I think everything is awful and should
be set on fire, and in the [next]({% post_url
programming/2019-02-13-my-build-system-is-wrong %}) I'm going to propose an alternative
that other people are going to think is terrible and should be set on fire.

You need to learn some weird (and usually awful) DSL
----------------------------------------------------
Why the hell do I need to learn some godawful DSL some high or drunk programmer
came up with in an afternoon when I already know C++?

Think about it, the syntax for Makefiles is terrible, the syntax for CMake
looks like someone started using a C preprocessor to _streamline_ their
`Makefile` generation, and then thought it was a good idea to extend into its
own program.

And don't get me started on build systems that use formats that were never
meant for direct human consumption (looking at you JSON and XML).

You need external tools, that you have to install
-------------------------------------------------
Why should I have to install external dependencies for building a C++ program?

Oh, your fancy build system doesn't need a stupid DSL? What do you say? It uses
Python/Groovy/node.js/Ruby/COBOL as a scripting language? Why, that's
wonderful, let me install **ANOTHER** toolchain to build my C/C++ project. Oh
wait, the toolchain for the scripting language needs Perl and Python and a
shell to be built? And also a C compiler I guess. Why are you doing this to me?

Do you know what tool every C/C++ programmer has always installed aside from a
text editor? A fucking C/C++ compiler, because, and this might come as news to
you, they're going to build a C/C++ project.

It should not be a task runner
------------------------------
Most build sytems seem to conflate building things and running tasks, for
instance you don't want your build system to create packages for distributions,
or running tests, or doing whatever, you want your build system to build, and
that is it.

It must handle third-party dependencies
---------------------------------------
It doesn't need to be fancy, but if your build system can't download
third-party libraries and other dependencies and build them, then it's garbage.

No, I don't want to use git submodules, they're terrible, and no, I don't want
to just run `curl` in my `Makefile`, that's a terrible idea. And I seriously
hope you didn't just mutter the word _container_.

Building in different configurations or for entirely different targets is hell
------------------------------------------------------------------------------
A build system should handle different configurations depending on the target
and handle cross-compilation as easily as it handles building for the host.

This is especially important for embedded, but it's also important for desktops
and other garbage.

Modifying a dependency is practically impossible
----------------------------------------------
Ever needed to change a line in a dependency of a dependency, or add a file, or
do something before or after building?  Sucks to be you, you can't.

And if you just told me to just vendor all dependencies in tree, go back to
whatever giant corporation you came from, and never talk to me or my duck
ever again.
