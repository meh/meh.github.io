---
title: meh, working projects
lang: en

layout: default
comments: false
---

<center class="section">DAEMONS</center><hr/>

[LOLastfm](https://github.com/meh/LOLastfm)
-------------------------------------------
LOLastfm is a very extensible last.fm scrobbler, it's written in Ruby and supports a virtually
infinite list of players.

You can easily inspect and change songs before they are scrobbled and you can implement commands
and whatnot.

You can talk with LOLastfm through a UNIX socket and thus execute commands.

You can implement commands directly in the configuration file or you can import already existing
commands, for instance you can retrieve lyrics for the current playing or any song by including
the *glyr* set of commands.

This is particularly useful to implement scripts and whatnot that are related to song listening.

<hr/>

[herpes](https://github.com/meh/herpes)
---------------------------------------
herpes is a scriptable event handling program, you can basically execute a Ruby block every certain
amount of time or after a certain amount of time.

It also supports various modules for generating events and for handling those events.

For example you can easily setup a script to check RSS feeds and send the updates through email.

<hr/>

[arpoon](https://github.com/meh/arpoon)
---------------------------------------
arpoon is an ARP event handling library, it fires events when the various types of ARP packets
are seen by the given interfaces.

With that you can easily implement ARP spoofing alerting or check when someone connects to your
network.

<hr/>

[tortard](https://github.com/meh/tortard)
-----------------------------------------
tortard is a simple SOCKS proxy bridging program configurable in Ruby.

It was originally made to map Tor hidden services to local ports to connect through irssi.

It supports internal and external SSL, this means that it can use SSL when connecting to the
specified `host:port` pair and it can also require SSL for you to connect to the local port.

It can be used with any SOCKS supporting server and be used to map any `host:port` pair to a local
port.

<center class="section">FIREFOX ADDONS</center><hr/>

[Smart Referer](https://addons.mozilla.org/en-US/firefox/addon/smart-referer/)
------------------------------------------------------------------------------
Smart Referer is a simple restartless addon that adds some privacy with referers.

It sends the referer only when staying on the same domain, thus keeping websites functional
and privacy while going outside intact.

You can configure it to be a bit lax about subdomains and it supports whitelisting.

<hr/>

[Blender](https://addons.mozilla.org/en-US/firefox/addon/blender-1/)
--------------------------------------------------------------------
Blender is a simple restartless addon that makes websites think you're using the most common
Firefox version, operating system and other stuff.

It's made to try counter browser fingerprinting.

<center class="section">RUBY GEMS</center><hr/>

[clj](https://github.com/meh/ruby-clj)
--------------------------------------
clj is a json-like gem to handle clojure s-expressions, it can parse all the supported
clojure literals and can easily generate clojure s-expressions from native Ruby objects.

<hr/>

[threadpool](https://github.com/meh/ruby-threadpool)
-----------------------------------------------
threadpool is a simple ThreadPool implementation written properly, it supports interrupting
tasks after they've been queued and some other stuff.

<hr/>

[ffi-inline](https://github.com/meh/ruby-ffi-inline)
----------------------------------------------------
ffi-inline is a gem that lets you easily inline C and C++ and makes it fairly easy
to support other languages that can create shared objects.

It uses ffi to then bind the shared object and make it usable from Ruby.

It makes it very easy to wrap C++ APIs instead of using SWIG or a handwritten C-ext.

<hr/>

[tesseract-ocr](https://github.com/meh/ruby-tesseract-ocr)
----------------------------------------------------------
tesseract-ocr is a very nice OCR library formerly from HP and now maintained by Google.

The gem is merely a wrapper to the advanced API which it supports completely in a Ruby-esque
way.

<hr/>

[glyr](https://github.com/meh/ruby-glyr)
----------------------------------------
glyr is a very nice metadata fetching library for various music related stuff.

The gem is a wrapper and rubyification of the C API.

<hr/>

[sensors](https://github.com/meh/ruby-sensors)
----------------------------------------------
sensors is a simple wrapper and rubyfication of libsensors.

<hr/>

[call-me](https://github.com/meh/ruby-call-me)
----------------------------------------------
call-me is a gem consisting of various method call related additions, it supports
memoization, named parameters, simple stupid pattern matching and method overloading.

<hr/>

[refining](https://github.com/meh/ruby-refining)
------------------------------------------------
refining is a gem that makes it easy to refine methods instead of going the alias-and-define
way that ends up causing issues.

<hr/>

[refr](https://github.com/meh/ruby-refr)
----------------------------------------
refr makes it possible to store and change variables in the current scope, it can also be used
as a StrongRef object.

Keep in mind that using Reference to a local variable will keep the scope alive, thus keeping alive
all the objects referenced in that scope, so use it carefully.

<hr/>

[retarded](https://github.com/meh/ruby-retarded)
------------------------------------------------
retarded is a simple gem that lets you create a Retarded object by passing a block, as soon as a
method is called on it the block is executed and its return value stored and it will keep
proxying method calls to that return value.

<hr/>

[bitmap](https://github.com/meh/ruby-bitmap)
-------------------------------------------------
bitmap is a simple Ruby library to handle bitmaps also known as bit arrays.

<hr/>

[moc](https://github.com/meh/ruby-moc)
-------------------------------------------
moc is a simple controller library, it fully implements the moc protocol and is usable to control
and check the status of the player.

It can also enter an event loop thus receiving all the events coming from the server, making it
easy to implement a different interface for moc.

<hr/>

[cmus](https://github.com/meh/ruby-cmus)
----------------------------------------
cmus is a simple controller library for cmus, cmus remote controlling lets you execute any
cmus command.

It has a nice ruby-esque feeling and is compatible with the moc controller interface.

<hr/>

[mpd](https://github.com/meh/ruby-mpd)
--------------------------------------
mpd is a controller library for MPD, it supports all the currently existing protocol commands and
wraps them in an easy to use API.

It's compatible with moc and cmus controller interface, although a lot richer.
