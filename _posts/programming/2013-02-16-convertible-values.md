---
title: C++ convertible value pattern
categories: [programming, cxx]
layout: post
---

This may be obvious to many C++ programmers, but I found it useful while
working on [gloglotto][1] to pass angles usable in multiple conventions
(degrees, radians and hours), so here it goes.

It's basically how `chrono` does in C++11.

```c++
#include <iostream>
#include <cmath>

class angle;

template <class Type>
double angle_cast (angle value);

class angle final
{
  public:
    class degrees final
    {
      public:
        static angle make (double value)
        {
          return value;
        }
    };

    class radians final
    {
      public:
        static angle make (double value)
        {
          return value * (180 / M_PI);
        }
    };

    class hours final
    {
      public:
        static angle make (double value)
        {
          return value * (1.0 / 15.0);
        }
    };

  private:
    angle (double value)
    {
      _degrees = value;
    }
    
    friend class degrees;
    friend class radians;
    friend class hours;

    template <class Type>
    friend double angle_cast (angle value);

  private:
    double _degrees;
};

template <>
inline
double
angle_cast<angle::degrees> (angle value)
{
  return value._degrees;
}

template <>
inline
double
angle_cast<angle::radians> (angle value)
{
  return value._degrees * (M_PI / 180);
}

template <>
inline
double
angle_cast<angle::hours> (angle value)
{
  return value._degrees * 15.0;
}

int
main (int argc, char* argv[])
{
  std::cout << "10 hours in degrees: " <<
    angle_cast<angle::degrees>(angle::hours::make(10)) << std::endl;

  std::cout << "1 radian in degrees: " <<
    angle_cast<angle::degrees>(angle::radians::make(1)) << std::endl;
  
  return 0;
}
```

If we really want, we can add some C++11 goodies too, and add operators to
create angles.

```c++
angle
operator "" _degrees (unsigned long long literal)
{
  return angle::degrees::make(literal);
}

angle
operator "" _degrees (long double literal)
{
  return angle::degrees::make(literal);
}

angle
operator "" _degree (unsigned long long literal)
{
  return angle::degrees::make(literal);
}

angle
operator "" _degree (long double literal)
{
  return angle::degrees::make(literal);
}

angle
operator "" _radians (unsigned long long literal)
{
  return angle::radians::make(literal);
}

angle
operator "" _radians (long double literal)
{
  return angle::radians::make(literal);
}

angle
operator "" _radian (unsigned long long literal)
{
  return angle::radians::make(literal);
}

angle
operator "" _radian (long double literal)
{
  return angle::radians::make(literal);
}

angle
operator "" _hours (unsigned long long literal)
{
  return angle::hours::make(literal);
}

angle
operator "" _hours (long double literal)
{
  return angle::hours::make(literal);
}

angle
operator "" _hour (unsigned long long literal)
{
  return angle::hours::make(literal);
}

angle
operator "" _hour (long double literal)
{
  return angle::hours::make(literal);
}

int
main (int argc, char* argv[])
{
  std::cout << "10 hours in degrees: " <<
    angle_cast<angle::degrees>(10_hours) << std::endl;

  std::cout << "1 radian in degrees: " <<
    angle_cast<angle::degrees>(1_radian) << std::endl;
  
  return 0;
}
```

Quite a bit more verbose implementation, but way less verbose and clearer
usage.

[1]: https://github.com/meh/gloglotto
