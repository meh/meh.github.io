---
title: C++11 lambda to function pointer or std::function
categories: [programming, magic]
layout: post
---

I recently needed to store callbacks in a map, so I needed a proper way to get
a common castable object from a lambda, so either a function pointer or an
std::function.

Now, lambdas in C++ are instances of an anonymous class specifically created
for each lambda object, this means that two lambdas with the same code are
actually different objects.

This also means that you can't pass lambdas around without using a template
because there's no lambda type.

Non capturing lambdas (the ones with nothing inside the `[]`) can be converted
to their function pointer by casting them with the corresponding signature,
while capturing ones can be wrapped in std::function.

This means you can do:

```c++
auto lambda = [](int a, float b) {
  std::cout << "a: " << a << std::endl;
  std::cout << "b: " << b << std::endl;
};

auto function  = static_cast<void (*)(int, float)>(lambda);
function(23, 5.4);

auto function2 = static_cast<std::function<void(int, float)>>(lambda);
function2(23, 5.4);
```

And it will work properly, `function` being a raw pointer to the function and
`function2` being an std::function object.

This also means that we can cast `function` to a more general pointer like
`(void (*)())` and `function2` to `void*` and store it in a struct and it will
be subsequentially castable to a working function, knowing the types to call it
with.

The issue arises when you don't know the signature of the lambda, you can only
cast knowing the signature, and I didn't want to have to write the parameters
twice, so here comes the solution to get the proper signature out of a lambda:

```c++
template <typename Function>
struct function_traits
  : public function_traits<decltype(&Function::operator())>
{};

template <typename ClassType, typename ReturnType, typename... Args>
struct function_traits<ReturnType(ClassType::*)(Args...) const>
{
  typedef ReturnType (*pointer)(Args...);
  typedef std::function<ReturnType(Args...)> function;
};

template <typename Function>
typename function_traits<Function>::pointer
to_function_pointer (Function& lambda)
{
  return static_cast<typename function_traits<Function>::pointer>(lambda);
}

template <typename Function>
typename function_traits<Function>::function
to_function (Function& lambda)
{
  return static_cast<typename function_traits<Function>::function>(lambda);
}
```

With this, we can now get the function pointer out of the lambda or wrap it in
an std::function, and store it.

To call that function we can then do something like this:

```c++
template <typename... Args>
void
call (void (*function)(), Args... args)
{
  static_cast<void (*)(Args...)>(function)(args...);
}

template <typename... Args>
void
call (void* function, Args... args)
{
  (*static_cast<std::function<void(Args...)>*>(function))(args...);
}
```

Bam, we have callbacks that are called and used normally, instead of reverting
to `cstdarg` and `va_list`.

Type safe callbacks (full example, supports capturing lambdas)
--------------------------------------------------------------
So far calls are not type safe, as in they can be casted to handle whatever
arguments even if the lambda didn't have those argument types or argument
number, this can lead to very weird bugs and this isn't a very nice solution,
this is C++ not C.

We can use `typeid` to ensure the arguments are of the appropriate type and
number.

```c++
#include <iostream>
#include <functional>
#include <stdexcept>
#include <typeinfo>
#include <string>
#include <map>

template <typename Function>
struct function_traits
  : public function_traits<decltype(&Function::operator())>
{};

template <typename ClassType, typename ReturnType, typename... Args>
struct function_traits<ReturnType(ClassType::*)(Args...) const>
{
  typedef ReturnType (*pointer)(Args...);
  typedef std::function<ReturnType(Args...)> function;
};

template <typename Function>
typename function_traits<Function>::pointer
to_function_pointer (Function& lambda)
{
  return static_cast<typename function_traits<Function>::pointer>(lambda);
}

template <typename Function>
typename function_traits<Function>::function
to_function (Function& lambda)
{
  return static_cast<typename function_traits<Function>::function>(lambda);
}

class callbacks final
{
  struct callback final
  {
    void*                 function;
    const std::type_info* signature;
  };

  public:
    callbacks (void)
    {
    }

    ~callbacks (void)
    {
      for (auto entry : _callbacks) {
        delete static_cast<std::function<void()>*>(entry.second.function);
      }
    }

    template <typename Function>
    void
    add (std::string name, Function lambda)
    {
      if (_callbacks.find(name) != _callbacks.end()) {
        throw std::invalid_argument("the callback already exists");
      }

      auto function = new decltype(to_function(lambda))(to_function(lambda));

      _callbacks[name].function  = static_cast<void*>(function);
      _callbacks[name].signature = &typeid(function);
    }

    void
    remove (std::string name)
    {
      if (_callbacks.find(name) == _callbacks.end()) {
        return;
      }

      delete static_cast<std::function<void()>*>(_callbacks[name].function);
    }

    template <typename ...Args>
    void
    call (std::string name, Args... args)
    {
      auto callback = _callbacks.at(name);
      auto function = static_cast<std::function<void(Args...)>*>(
        callback.function);

      if (typeid(function) != *(callback.signature)) {
        throw std::bad_typeid();
      }

      (*function)(args...);
    }

  private:
    std::map<std::string, callback> _callbacks;
};

int
main (int argc, char* argv[])
{
  callbacks cb;

  cb.add("lol", [](int a, float b) {
    std::cout << "a: " << a << std::endl;
    std::cout << "b: " << b << std::endl;
  });

  cb.call("lol", 23, 5.4f);
  cb.call("lol", 23, 43);

  return 0;
}
```

Explanation of the magic
------------------------ 
The first `function_traits` inherits from the second, it just simplifies the
definition of the function type.

Every lambda objects has an `operator()` method which is used to call the
lambda, this means that its definition contains both the return type and the
parameters types.

The `decltype` of a method, is composed of the class, the return type and the
list of arguments types.

The decltype is used to define a typedef for a function pointer with the proper
return type and arguments types inferred from the `decltype` of the method.

`to_function_pointer` and `to_function` do nothing else but use the typedef
defined in the `function_traits` template to then cast the lambda to its
function pointer.

Bam, magic.
