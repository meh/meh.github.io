---
title: C++ property like accessors
categories: [programming, cxx]
layout: post
---

Again something most C++ programmers would know but that I find incredibly
useful in certain cases.

```c++
#include <iostream>
#include <algorithm>

template <int Size>
class vector
{
  public:
    vector (void) : _data(new int[Size]),
                    x(Size >= 1 ? _data[0] : _guard[0]),
                    y(Size >= 2 ? _data[1] : _guard[0]),
                    z(Size >= 3 ? _data[2] : _guard[0]),
                    w(Size >= 4 ? _data[3] : _guard[0])
    {
      std::fill_n(_data, Size, 0);
    }

  private:
    int* _guard[0];
    int* _data;

  public:
    int& x;
    int& y;
    int& z;
    int& w;
};

int
main (int argc, char* argv[])
{
  vector<3> bar;

  std::cout << bar.x << std::endl;
  bar.x = 3;
  std::cout << bar.x << std::endl;

  return 0;
}
```

I don't think it's possible to define stuff in templates only according to its
parameters, if it were possible one could avoid the `_guard` business and just
define the property when it's present, such is life.

This can make vector usage a bit easier on the eyes instead of having streams
of `[]` accesses.
