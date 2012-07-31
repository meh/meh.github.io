---
title: meh, working projects
lang: en

layout: default
comments: false
---

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

<hr/>

[Smart Referer](https://addons.mozilla.org/en-US/firefox/addon/smart-referer/)
------------------------------------------------------------------------------
Smart Referer is a Firefox addon that adds some privacy with referers.

It sends the referer only when staying on the same domain, thus keeping websites functional
and privacy while going outside intact.

You can configure it to be a bit lax about subdomains and it supports whitelisting.

<hr/>

[Blender](https://addons.mozilla.org/en-US/firefox/addon/blender-1/)
--------------------------------------------------------------------
Blender is a simple Firefox addon that makes websites think you're using the most common
Firefox version, operating system and other stuff.

It's made to try counter browser fingerprinting.
