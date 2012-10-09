---
title: C magic header
categories: [programming, c]
layout: post
---

When playing around with datashit, an experimental C library to support
containers and functional programming style, I needed to make the library
usable, and to achieve that I needed to support optional and default
parameters.

After this you might take me for crazy: optional and default parameters in C,
what's this heresy?

Well, I came up with a simple header that makes that easily doable, thus
improving usability of libraries.

{% highlight c %}
#include <stdlib.h>

#define ARGS_LENGTH(...)  ARGS_LENGTH_(__VA_ARGS__, ARGS_LENGTH_SEQ)
#define ARGS_LENGTH_(...) ARGS_LENGTH_N(__VA_ARGS__)

#define ARGS_LENGTH_N( \
   _1,  _2,  _3,  _4,  _5,  _6,  _7,  _8,  _9, _10, \
  _11, _12, _13, _14, _15, _16, _17, _18, _19, _20, \
  _21, _22, _23, _24, _25, _26, _27, _28, _29, _30, \
  _31, _32, _33, _34, _35, _36, _37, _38, _39, _40, \
  _41, _42, _43, _44, _45, _46, _47, _48, _49, _50, \
  _51, _52, _53, _54, _55, _56, _57, _58, _59, _60, \
  _61, _62, _63, N, ...) N

#define ARGS_LENGTH_SEQ \
                          63, 62, 61, 60, \
  59, 58, 57, 56, 55, 54, 53, 52, 51, 50, \
  49, 48, 47, 46, 45, 44, 43, 42, 41, 40, \
  39, 38, 37, 36, 35, 34, 33, 32, 31, 30, \
  29, 28, 27, 26, 25, 24, 23, 22, 21, 20, \
  19, 18, 17, 16, 15, 14, 13, 12, 11, 10, \
   9,  8,  7,  6,  5,  4,  3,  2,  1,  0

#define ARGS_FIRST_AS(type, ...)  ((type) ARGS_FIRST(__VA_ARGS__))
#define ARGS_FIRST(...) ARGS_FIRST_(__VA_ARGS__, ARGS_NULL_SEQ)
#define ARGS_FIRST_(...) ARGS_FIRST__(__VA_ARGS__)
#define ARGS_FIRST__(a, ...) (a)

#define ARGS_SECOND_AS(type, ...) ((type) ARGS_SECOND(__VA_ARGS__))
#define ARGS_SECOND(...) ARGS_SECOND_(__VA_ARGS__, ARGS_NULL_SEQ)
#define ARGS_SECOND_(...) ARGS_SECOND__(__VA_ARGS__)
#define ARGS_SECOND__(a, b, ...) (b)

#define ARGS_THIRD_AS(type, ...)  ((type) ARGS_THIRD(__VA_ARGS__))
#define ARGS_THIRD(...) ARGS_THIRD_(__VA_ARGS__, ARGS_NULL_SEQ)
#define ARGS_THIRD_(...) ARGS_THIRD__(__VA_ARGS__)
#define ARGS_THIRD__(a, b, c, ...) (c)

#define ARGS_FOURTH_AS(type, ...) ((type) ARGS_FOURTH(__VA_ARGS__))
#define ARGS_FOURTH(...) ARGS_FOURTH_(__VA_ARGS__, ARGS_NULL_SEQ)
#define ARGS_FOURTH_(...) ARGS_FOURTH__(__VA_ARGS__)
#define ARGS_FOURTH__(a, b, c, d, ...) (d)

#define ARGS_FIFTH_AS(type, ...)  ((type) ARGS_FIFTH(__VA_ARGS__))
#define ARGS_FIFTH(...) ARGS_FIFTH_(__VA_ARGS__, ARGS_NULL_SEQ)
#define ARGS_FIFTH_(...) ARGS_FIFTH__(__VA_ARGS__)
#define ARGS_FIFTH__(a, b, c, d, e, ...) (e)

#define ARGS_NULL_SEQ \
  NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, \
  NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, \
  NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL
{% endhighlight %}

Here's a simple example of how it makes life easier with variadic functions:

{% highlight c %}
#include <stdio.h>
#include <stdarg.h>
#include "magic.h"

int
lol (int n, ...)
{
  va_list args;
  int     result = 0;

  va_start(args, n);

  for (size_t i = 0; i < n; i++) {
    result += va_arg(args, int);
  }

  va_end(args);

  return result;
}

#define lol(...) lol(ARGS_LENGTH(__VA_ARGS__), __VA_ARGS__)

int
main (int argc, char* argv[])
{
  printf("%d\n", lol(2, 4, 8));

  return 0;
}
{% endhighlight %}

{% highlight bash %}
> gcc -std=gnu99 -o lol lol.c
> ./lol
14
{% endhighlight %}

As you can see, doing it like that you don't have to pass the number of
arguments as first parameter or pass a guard to know where the last parameter
is, just some preprocessor magic and we have that automatically.

Here's an example for optional parameters:

{% highlight c %}
#include <stdio.h>
#include <stdarg.h>
#include "magic.h"

int
lol (int a, int b, int c)
{
  return a + b + c;
}

#define lol(...) (\
  (ARGS_LENGTH(__VA_ARGS__) == 1 ?\
    lol(1, 2, ARGS_FIRST_AS(int, __VA_ARGS__)) :\
\
  (ARGS_LENGTH(__VA_ARGS__) == 2 ?\
    lol(1, ARGS_FIRST_AS(int, __VA_ARGS__), ARGS_SECOND_AS(int, __VA_ARGS__)) :\
\
  lol(ARGS_FIRST_AS(int, __VA_ARGS__), ARGS_SECOND_AS(int, __VA_ARGS__),\
    ARGS_THIRD_AS(int, __VA_ARGS__))))\
)

int
main (int argc, char* argv[])
{
  printf("%d\n", lol(3));
  printf("%d\n", lol(2, 3));
  printf("%d\n", lol(1, 2, 3));

  return 0;
}
{% endhighlight %}

{% highlight bash %}
> gcc -std=gnu99 -Wno-pointer-to-int-cast -o lol lol.c
> ./lol
6
6
6
{% endhighlight %}

For optional parameters follow the example with default parameters but pass as
parameter soemthing you know means nothing has been passed.

Understanding the magic
-----------------------
When `ARGS_LENGTH` is called `ARGS_LENGTH_` gets called with
`parameteres + ARGS_LENGTH_SEQ`, for `1, 2, 3` you would get
`1, 2, 3, 63, 62...`.

At that point `ARGS_LENGTH_N` is called with the two sequences concatenated and
the number of arguments is found in N, because the more parameters the more the
sequence shifts to the right.

`ARGS_FIRST` etcetera use a similar concept, except to ensure a result is yield
a sequence of `NULL` is used.
