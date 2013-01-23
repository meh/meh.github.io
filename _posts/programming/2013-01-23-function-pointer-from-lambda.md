---
title: C++11 lambda to function pointer
categories: [programming, magic]
layout: post
---

I recently needed to store callbacks in a map, so I needed a proper way to get
a function pointer out of a lambda.

Now, lambdas in C++ are instances of an anonymous class specifically created
for each lambda object, this means that two lambdas with the same code are
actually different objects.

This also means that you can't pass lambdas around without using a template
because there's no lambda type.

Non capturing lambdas (the ones with nothing inside the `[]`) can be converted
to their function pointer by casting them with the corresponding signature.

This means you can do:

```c++
auto lambda = [](int a, float b) {
  std::cout << "a: " << a << std::endl;
  std::cout << "b: " << b << std::endl;
};

auto function = (void (*)(int, float)) lambda;

function(23, 5.4);
```

And it will work properly, function being a raw pointer to the function.

This also means that we can cast function to a more general pointer like `(void
(*)())` and store it in a struct and it will be subsequentially castable to a
working function, knowing the types to call it with.

The issue arises when you don't know the signature of the lambda, you can only cast
knowing the signature, and I didn't want to have to write the parameters twice, so
here comes the solution to get the proper signature out of a lambda:

```c++
template <typename Function>
struct function_traits
  : public function_traits<decltype(&Function::operator())>
{};

template <typename ClassType, typename ReturnType, typename... Args>
struct function_traits<ReturnType(ClassType::*)(Args...) const>
{
  typedef ReturnType (*signature)(Args...);
};

template <typename Function>
typename function_traits<Function>::signature
to_function_pointer (Function& lambda)
{
  return (typename function_traits<Function>::signature) lambda;
}
```

With this, we can now get the function pointer out of the lambda, and store it.

To call that function we can then do something like this:

```c++
template <typename... Args>
void
call (void (*function)(), Args... args)
{
  ((void (*)(Args...)) function)(args...);
}
```

Bam, we have callbacks that are called and used normally, instead of reverting
to `cstdarg` and `va_list`.

*Note: you still have to make sure the passed parameters are of the appropriate
type, unless you want to use `std::type_info` to check them automatically.*

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

`to_function_pointer` does nothing else but use the typedef defined in the
`function_traits` template to then cast the lambda to its function pointer.

Bam, magic.
