---
title: Stringify all things in C++
categories: [programming, cxx]
layout: post
---

While working on [amirite][1] I needed a nice way to print strings from any kind
of incoming data to make assertions look decent, here goes nothing:

```c++
namespace stringify
{
# ifndef NO_DEMANGLE
#   include <cxxabi.h>

    template <typename Type>
    std::string
    type (void)
    {
      std::string result;
      int         status;
      char*       demangled = abi::__cxa_demangle(typeid(Type).name(),
                                                    nullptr, nullptr, &status);

      result = demangled;
      free(demangled);

      return result;
    }
# else
    template <typename Type>
    std::string
    type (void)
    {
      return typeid(Type).name();
    }
# endif

  namespace _has_cout
  {
    typedef char no[1];
    typedef char yes[2];

    struct any
    {
      template <typename T>
      any (T const&);
    };

    no& operator << (std::ostream const&, any const&);

    yes& test (std::ostream&);
    no&  test (no&);

    template <typename Type>
    struct has_cout
    {
      static std::ostream& s;
      static Type const& t;
      static bool const value = sizeof(test(s << t)) == sizeof(yes);
    };
  }

  template <typename Type>
  struct has_cout : _has_cout::has_cout<Type>
  {};

  template <typename Type>
  struct has_to_string
  {
    typedef char no[1];
    typedef char yes[2];

    template <typename C>
    static typename std::enable_if<std::is_same<decltype(&C::to_string),
                                   std::string(C::*)(void)>::value, yes&>::type
    test (decltype(&C::to_string));

    template <typename C>
    static no& test (...);

    static const bool value = sizeof(test<Type>(0)) == sizeof(yes);
  };

  template <typename Type>
  struct is_string
  {
    static const bool value = std::is_same<Type, std::string>::value ||
                              std::is_same<Type, std::wstring>::value ||
                              std::is_same<Type, std::u16string>::value ||
                              std::is_same<Type, std::u32string>::value;
  };

  template <typename Type>
  struct is_raw_string
  {
    static const bool value = (std::is_array<Type>::value &&
      std::is_same<typename std::remove_const<
        typename std::remove_extent<Type>::type>::type, char>::value) ||

      (std::is_pointer<Type>::value &&
        std::is_same<typename std::remove_const<
          typename std::remove_pointer<Type>::type>::type, char>::value);
  };

  template <typename Type>
  typename std::enable_if<is_string<Type>::value, Type>::type
  value (Type& value)
  {
    return value;
  }

  template <typename Type>
  typename std::enable_if<is_raw_string<Type>::value, std::string>::type
  value (Type& value)
  {
    return value;
  }

  template <typename Type>
  typename std::enable_if<std::is_fundamental<Type>::value, std::string>::type
  value (Type value)
  {
    std::ostringstream ss(std::ostringstream::out);
    ss << std::boolalpha << value;

    return ss.str();
  }

  template <typename Type>
  typename std::enable_if<std::is_pointer<Type>::value &&
    !is_raw_string<Type>::value, std::string>::type
  value (Type& value)
  {
    std::ostringstream ss(std::ostringstream::out);
    ss << "(" << type<Type>() << ") " << value;

    return ss.str();
  }

  template <typename Type>
  typename std::enable_if<std::is_enum<Type>::value &&
    has_cout<Type>::value, std::string>::type
  value (Type value)
  {
    std::ostringstream ss(std::ostringstream::out);
    ss << "#<enum " << type<Type>() << ": " << value << ">";

    return ss.str();
  }

  template <typename Type>
  typename std::enable_if<std::is_enum<Type>::value &&
    !has_cout<Type>::value, std::string>::type
  value (Type value)
  {
    std::ostringstream ss(std::ostringstream::out);
    ss << "#<enum " << type<Type>() << ": " <<
      static_cast<typename std::underlying_type<Type>::type>(value) << ">";

    return ss.str();
  }

  template <typename Type>
  typename std::enable_if<std::is_union<Type>::value &&
    has_cout<Type>::value, std::string>::type
  value (Type& value)
  {
    std::ostringstream ss(std::ostringstream::out);
    ss << "#<union " << type<Type>() << ": " << value << ">";

    return ss.str();
  }

  template <typename Type>
  typename std::enable_if<std::is_union<Type>::value &&
    !has_cout<Type>::value, std::string>::type
  value (Type& value)
  {
    std::ostringstream ss(std::ostringstream::out);
    ss << "#<union " << type<Type>() << ":" << &value << ">";

    return ss.str();
  }

  template <typename Type>
  typename std::enable_if<std::is_class<Type>::value &&
    !is_string<Type>::value &&
    !has_to_string<Type>::value &&
    has_cout<Type>::value, std::string>::type
  value (Type& value)
  {
    std::ostringstream ss(std::ostringstream::out);
    ss << "#<" << type<Type>() << ": " << value << ">";

    return ss.str();
  }

  template <typename Type>
  typename std::enable_if<std::is_class<Type>::value &&
    !has_to_string<Type>::value &&
    !is_string<Type>::value &&
    !has_cout<Type>::value, std::string>::type
  value (Type& value)
  {
    std::ostringstream ss(std::ostringstream::out);
    ss << "#<" << type<Type>() << ":" << &value << ">";

    return ss.str();
  }

  template <typename Type>
  typename std::enable_if<has_to_string<Type>::value, std::string>::type
  value (Type& value)
  {
    return value.to_string();
  }

  template <typename Type>
  typename std::enable_if<std::is_array<Type>::value &&
    !is_raw_string<Type>::value, std::string>::type
  value (Type& value)
  {
    std::ostringstream ss(std::ostringstream::out);

    ss << "[";
    for (unsigned i = 0; i < std::extent<Type>::value; i++) {
      ss << stringify::value(value[i]);

      if (i < std::extent<Type>::value - 1) {
        ss << ", ";
      }
    }
    ss << "]";

    return ss.str();
  }
}
```

An example stringifying things:

```c++
class foo
{};

class bar
{
  public:
    std::string
    to_string (void)
    {
      return "#<bar: dabbah>";
    }
};

enum class derp
{
  lol, wut, omg
};

std::ostream& operator << (std::ostream& on, derp v)
{
  switch (v) {
    case derp::lol:
      on << "lol";
      break;

    case derp::wut:
      on << "wut";
      break;

    case derp::omg:
      on << "omg";
      break;
  }

  return on;
}

int
main (int argc, char* argv[])
{
  std::string str = "34";
  std::cout << stringify::value(str) << std::endl;

  foo a;
  std::cout << stringify::value(a) << std::endl;

  bar b;
  std::cout << stringify::value(b) << std::endl;

  std::cout << stringify::value(23) << std::endl;
  std::cout << stringify::value(derp::omg) << std::endl;

  int lol[][2] = { { 1, 2 }, { 3, 4 } };
  std::cout << stringify::value(lol) << std::endl;

  float* duh = (float*) 342;
  std::cout << stringify::value(duh) << std::endl;

  std::cout << stringify::value("lol") << std::endl;

  std::cout << stringify::value(true) << std::endl;

  return 0;
}
```

And the output:

```text
34
#<foo:0x7fff253f8730>
#<bar: dabbah>
23
#<enum derp: omg>
[[1, 2], [3, 4]]
(float*) 0x156
lol
true
```

Bam, magic (as usual).

[1]: https://github.com/meh/amirite
