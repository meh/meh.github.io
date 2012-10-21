---
title: Coroutines in Zsh
categories: [random]
layout: post
---

While thinking about how to implement the Window Manager in Zsh, I had to come
up with a nice way to handle the socket handling loop outside in background and
to have as main loop the event handling one.

While researching I found out about coprocesses in Zsh, which are very awesome.

They basically create a duplex pipe with a process that runs in background, so
it's like having an implicit message queue that you can access by using
`{print,read} -p`.

Following is a stupid example that generates random numbers in parallel and
yields them to the main process.

Here's it in Go:

{% highlight go %}
package main

import (
  "fmt"
  "time"
  "math/rand"
)

func main() {
  rand.Seed(time.Now().UnixNano())

  c := make(chan int);

  go func() {
    for {
      c <- rand.Int() % 32767

      time.Sleep(1000 * time.Millisecond)
    }
  }();

  for {
    fmt.Printf("Here's a random number: %d\n", <-c)
  }
}
{% endhighlight %}

And here's in Zsh:

{% highlight bash %}
coproc {
  while true; do
    print $RANDOM

    sleep 1
  done
}

while read -p number; do
  print "Here's a random number: $number"
done
{% endhighlight %}

This is a retarded example, but it can be used for greater good; too bad you
can only have a single coprocess and implicit channel.

*By the way, goroutines are very nice, too bad the language is retarded on some
very important parts (no exception handling).*
