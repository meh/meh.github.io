---
title: Using Servo as a Cargo dependency
categories: [programming, rust, servo]
layout: post
---

I recently decided to create my own browser, exactly fit to my needs, because
the [future of Firefox addons][1] I strongly depend on is kind of uncertain.

Since I've also been using Rust quite heavily lately, I decided my browser would use
the next-generation engine [Servo][2].

Now, Servo uses Cargo, but the build system is also kept sane through mach, this means
using Servo as a dependency from a common Rust project is not that straightforward.

The first thing that has to be done is to get the specific Rust version Servo
is using, we have to do this because Servo heavily depends on Rust nightly
features and other internals that may change between one commit and the other.

We'll be using [multirust][3] to keep things sane and smooth.

```bash
> git clone --depth=1 https://github.com/servo/servo
> cd servo
> ./mach bootstrap-cargo
> ./mach bootstrap-rust
> cd .servo
> cd cargo
> mv * cargo
> tar czf cargo.tar.gz cargo
> mv cargo.tar.gz ..
> cd ..
> cd rust/*
> mv * rust
> tar czf rust.tar.gz rust
> mv rust.tar.gz ../..
> cd ../..
> multirust update servo --installer rust.tar.gz,cargo.tar.gz
```

Now that we have our Rust toolchain that matches the one used by Servo, add the
following dependency to your `Cargo.toml`.

```toml
[dependencies.servo]
git  = "https://github.com/servo/servo"
path = "components/servo"

default-features = false
```

This adds Servo as a dependency but it's not enough to make it compile, you
also have to copy the `Cargo.lock` in `components/servo` from the Servo
repository to your project, this is because `Cargo.lock` for libraries is
ignored, and Servo will likely fail to compile without the specific versions
it uses.

Now that the dependency is ready you can run `cargo build`, wait a couple eons
and it will be done.

To figure out how to actually use Servo my advice is to look [here][4] and
[here][4], or read the next post when it's ready.

[1]: https://blog.mozilla.org/addons/2015/08/21/the-future-of-developing-firefox-add-ons/
[2]: https://github.com/servo/servo
[3]: https://github.com/brson/multirust
[4]: https://github.com/servo/servo/tree/master/ports/glutin
[5]: https://github.com/servo/servo/tree/master/components/servo
