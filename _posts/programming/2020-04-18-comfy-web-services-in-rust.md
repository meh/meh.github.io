---
title: Comfy web services in Rust.
categories: [programming, rust, graphql]
layout: post
comments: 5
---

I think Rust is officially comfy as a language for backend and/or web service
development, but upcoming uncomfyness looms ever-present.

Here's a brief history of how **I** felt and what **I** thought writing backend
services in Rust (both professionally and not) for the past 5 years.

2015-2016
---------
Nice, Rust is finally stable, I don't have to fix my code every other day,
jolly good show!

Look, you got [hyper](https://lib.rs/crates/hyper), you can make HTTP speaking
stuff, and if you really feel like you need a web framework you even got
[Iron](https://lib.rs/crates/iron) for something Rusty and
[nickel](https://lib.rs/crates/nickel) if you prefer `express.js` style
frameworks.

Just pick a [postgres](https://lib.rs/crates/postgres), some
[serde](https://lib.rs/serde) and off you go, write all the things!

But wait, everything is synchronous, someone should do something about that, we
can't be [webscale](https://www.youtube.com/watch?v=b2F-DItXtZs) without
asynchronous I/O, right?

I heard about [mio](https://lib.rs/crates/mio) tho, maybe good things will
happen.

2017
----
Oh okay, there's this thing called [tokio](https://lib.rs/crates/tokio), it's a
little bit confusing even if I wrote reactors and async I/O libraries in the
past, but I guess I'm just too stupid to understand it.

Wait, when is `hyper` going to support this? Hopefully soon, I bet I'm going to
have to rewrite everything when that happens.

But what is this [Rocket](https://lib.rs/crates/rocket) thing people have been
raving about? Looks pretty neat, reminds me of Sinatra from Ruby, I like it,
too bad my shit is already using `Iron` and running on stable, really wish I
could use it.

All these other attempts at web frameworks don't really spark any
interest in me or push me to move over either.

[Gotham](https://lib.rs/crates/gotham) is a resonating _meh_, the only good
thing it has going for it is it runs on stable, but it's the same old stuff,
and still synchronous.

[rouille](https://lib.rs/crates/rouille) is kind of interesting and is coming
from one of my favorite Rust [peeps](https://github.com/tomaka), but it's still
synchronous (on purpose, love you tomaka).

[canteen](https://lib.rs/crates/canteen) is also nothing new, and I don't like
neither Flask nor Python, so there's that.

2018
----
Finally, `hyper` is asynchronous now, look at it go in the
[benchmarks](https://www.techempower.com/benchmarks)! `tokio` reactor goes
brrrrr.

What about `Iron` tho? It's forever unmaintained, sad. Libraries are starting
to use tokio, and now I have 3 different versions of `hyper` since I'm stuck
with `Iron` and [reqwest](https://lib.rs/crates/reqwest) uses asynchronous
`hyper`, great, I really wish I could move to something new, but what to?

`Rocket` is still synchronous and doesn't seem to be going to become
asynchronous any time soon (still hasn't, lmao), and what's the point of going
to nightly and staying synchronous if I'm going to have to rewrite everything
again anyway.

I heard about [actix-web](https://lib.rs/crates/actix-web) which works on
stable, but at this point I might as well just be using Erlang or Elixir. I
don't really think the actor model is the best fit for a web framework anyway,
and as an actor system [actix](https://lib.rs/crates/actix) seems to be missing
the point entirely, where are my supervision trees? Where is let it fail? It
just doesn't map well to Rust in its current form.

There's experimental nightly support for some form of async/await using macros,
could give it a shot with `tokio` and `hyper` and see where we end up. I bet
async/await is going to look very similar to this anyway, the rest will be
implementation details.

Okay, now I'd like something fancier than `postgres` to do my SQL stuff, I
heard many good things about [Diesel](https://lib.rs/crates/diesel), let me
look into it.

Okay, it's way too ORMy for me and the generated documentation is essentialy
unreadable, I'd rather be writing SQL, and I don't like the way the migration
tool works, and it's not asynchronous, and I'm going to nightly, to use `tokio`
and `hyper` with async/await macros, to then also be fudged, guess I'll just
use [tokio-postgres](https://lib.rs/crates/tokio-postgres).

As for migrations, I will just suffer and write a shitty Ruby script, suffering
is good.

At this point [warp](https://lib.rs/crates/warp) and
[juniper](https://lib.rs/crates/juniper) catch my eye, the APIs are still kind
of rough, but they show promise, my heart fills with hope as contingent dread
quietly broods in the shadows.

2019
----
Everyone seems to be working hard on stabilizing async/await and the ecosystem
is slowly moving towards using `tokio` and making all the things async, which
is good.

The ecosystem is now fractured between libraries using the latest `tokio` and
`std::future` stuff and the old `tokio` and `futures` stuff, which is bad.

Nothing much has happened in the realm of web frameworks, `Rocket` still isn't
async, `actix-web` is involved in a shitstorm with the community, all the web
frameworks that have been popping up were targeting stable Rust and so had to
be synchronous and be boring, providing nothing really new. In the meantime
`Diesel` is also still synchronous.

The end of the year approaches when **BAM!** async/await hits Rust stable,
everyone rejoices as the long and arduous thousands of hours communally spent
on this endeavour come to fruition.

**BAM!** [SQLx](https://lib.rs/crates/sqlx) is a thing, compile-time checked
SQL queries, async from the start, all the comfy query writing you ever needed.

**BAM!** [warp](https://lib.rs/crates/warp) is a thing, ultra-elegant async
mini-web framework that really takes advantage of what Rust has to offer, just
look at the glory of
[this](https://docs.rs/warp/0.2.2/warp/trait.Filter.html#method.and_then) and
how it's
[implemented](https://github.com/seanmonstar/warp/commit/5c269562a823c5340f3dfc14bdd11af592c03dea).

**BAM!** [juniper](https://lib.rs/crates/juniper) is a thing, streamlined
GraphQL development, async ready, with integration with `warp` and all things
good.

**BAM!** [movine](https://github.com/byronwasti/movine) is a thing, easy-peasy
migrations that actually make sense.

I finally had almost everything I wanted, compile-time checked SQL, type-safe
HTTP stuffs, GraphQL candies, easy migrations, and everything async!

Then, everything changed when the [async-std](https://lib.rs/async-std) nation
attacked.

2020
----
It felt like the async ecosystem was quickly increasing pace of maturing now
that async/await was on stable.

As such alternatives to `tokio` have been popping up, the major alternative to
it is `async-std`, I have to say I really like their approach of just taking
`std` APIs and trying to map them to be asynchronous as well as possible, and I
feel like `async-std` is better than `tokio` in many ways.

Despite how nice `async-std` is I still hold fear and hatred for it, because of
the added friction and fracture they created in the async ecosystem, I would
have rather had them massage the `tokio` guy enough to convince him to change
things around (_which would have probably been a useless endeavor, but alas_).

As things stand right now to write a reasonable backend you might end up with
`1 + N` runtimes running at the same time, this is because `async-std` ends up
spawning its own runtime in the background and `tokio` wants its runtime to be
running, too.

Library authors end up having to make a decision on which async runtime to
target, and you end up having to make libraries targeting different runtimes
work at the same time.

Now aside from the runtime issue that adds overhead, the `async-std` runtime
cannot run some futures generated by `tokio` (namely sockets), this is not
`async-std`'s fault but `tokio`'s as they use TLS contexts for performance
reasons. `tokio` can still run futures coming from `async-std` tho.

In the meantime other people have been working on new runtimes, my favorite at
the moment is
[smol](https://stjepang.github.io/2020/04/03/why-im-building-a-new-async-runtime.html).

The future holds potential for one of the best async experiences I can imagine
having, especially if they do something like [TCP-preserving
closures](https://boats.gitlab.io/blog/post/the-problem-of-effects/), but I
hope I won't have to spend the next 2 years worrying if I'll have to, once
again, ~~rewrite everything in Rust, but async, twice~~.

Time to get comfy
-----------------
Here is how it looks dicking around with mutations on the GraphQL Playground
(already provided by the service), docs and schemas and all the things!

[![Comfy GraphQL with Rust](/img/comfy-graphql.png)](/img/comfy-graphql.png)

Here's the list of materials and tools needed to get this comfy:

* [clap](https://lib.rs/crates/clap) + [dotenv](https://lib.rs/crates/dotenv)
  for passing configuration and such.
* [tracing](https://lib.rs/crates/tracing) for logging.
* [warp](https://lib.rs/crates/warp) for dealing with small REST and WebSocket
  parts.
* [juniper](https://lib.rs/crates/juniper) for speaking GraphQL and providing
  99.9% of the API (it uses [juniper_warp](https://lib.rs/crates/juniper_warp)
  to integrate with `warp`).
* [thiserror](https://lib.rs/crates/thiserror) +
  [anyhow](https://lib.rs/crates/anyhow) +
  [http-api-problem](https://lib.rs/crates/http-api-problem) for error
  handling/formatting.
* [biscuit](https://lib.rs/crates/biscuit) +
  [argonautica](https://lib.rs/crates/argonautica) for authentication.
* [sqlx](https://lib.rs/crates/sqlx) with PostgreSQL for all my relational
  needs.
* [redis](https://lib.rs/crates/redis) for session data (and whatever else
  comes up).
* [reqwest](https://lib.rs/crates/reqwest) for third-party REST APIs.

Following are some simplified, cut up and commented excerpts from web services
I've been working on.

Excerpts from `main`.
---------------------
The `main.rs` is mostly a bunch of warp route setup and CLI management, nothing
too fancy.

Here we can see all our comfy settings for our comfy web service, like database
URLs and secrets, this stuff is taken from the `.env` or environment if no
arguments are passed.

Not sure what I'm gonna do if I need more complicated configuration settings,
but probably just TOML.

```rust
#[derive(Clap, Debug)]
#[clap(name = "brrrrr",
  rename_all = "kebab-case",
  rename_all_env = "screaming-snake"
)]
struct Args {
  #[clap(short, long)]
  debug: bool,

  #[clap(required = true, short = "D", long, env)]
  database_url: String,
  #[clap(required = true, short = "R", long, env)]
  redis_url: String,

  #[clap(required = true, long, env)]
  jwt_secret: String,
  #[clap(required = true, long, env)]
  argon_secret: String,
  #[clap(long, env)]
  argon_iterations: Option<u32>,
  #[clap(long, env)]
  argon_memory_size: Option<u32>,
  #[clap(short, long, env)]
  session_lifetime: Option<i64>,

  #[clap(default_value = "127.0.0.1:3535")]
  host: SocketAddr,
}
```
How cool is this `tokio::main` thing, and then returning an `anyhow::Result`,
gone are the days where you had to set up runtimes manually and write your own
`exit` helper.

```rust
#[tokio::main]
async fn main() -> anyhow::Result<()> {
```
Initialize stuff, I'd be using `paw` but `dotenv` doesn't play well with it, so
manual shit for now.

```rust
  tracing_subscriber::fmt::init();
  dotenv::dotenv()?;
```

Set up the `Environment`, here by environment I mean the set of common services
the backend needs, for example password hashing, database access, email
sending, external services, and whatever else might be needed.

I'm not gonna post a definition for it because it's just boilerplate setting up
and returning connection pools and clients and other garbage.

```rust
  let args = Args::parse();
  let env  = Environment::new(&args);
```

The `Environment` is usually sent along `warp::Filter`s, so wrap it in
one.

```rust
  let env = warp::any().map(move || env.clone());
```

This sets up CORS for us, and we never have to worry about it again, and
`warp::with` is just lovely.

```rust
  let cors = warp::cors()
    .allow_methods(vec!["GET", "POST"])
    .allow_header("content-type")
    .allow_header("authorization")
    .allow_any_origin()
    .build();
```

This makes all the requests be logged using the `log` crate, which end up
getting eaten up by `tracing`, which then prints them out.

```rust
  let log = warp::log("brrrrr::request");
```

This sets up the (so far) only non-GraphQL request, and it's for
authentication.

Just look at how cool this is, look at it! `Environment` passed along at every
request, extract the body to JSON which gets directly deserialized (!) and the
remote address for fingerprinting, because why not.

```rust
  let auth = warp::post()
    .and(warp::path("auth"))
    .and(env.clone())
    .and(warp::body::json())
    .and(warp::addr::remote())
    .and_then(|env, req, addr| async move {
      auth::filter(env, req, addr).await
        .map_err(problem::build)
    });
```

Some grouping for sanity, the way `warp` lets you compose things is just
orgasmic to me.

```rust
  let graphql = {
     use juniper_warp::{*, subscriptions::*};
     use juniper_subscriptions::*;
```

Here I'm creating an `auth` filter that will resolve to an `Option<(String,
String)>` and I can reuse it in any other filter.

Now if Rust had anonymous structs this would be even better.

```rust
     #[derive(Deserialize, Debug)]
     struct Query {
       csrf: Option<String>
     }

     let auth = warp::header::optional("authorization")
       .or(warp::cookie::optional("jwt"))
       .unify()
       .and(warp::query())
       .and_then(|jwt: Option<String>, query: Query| {
         if jwt.is_none() && query.csrf.is_none() {
           return future::ok(None);
         }

         if jwt.is_none() || query.csrf.is_none() {
           return future::err(problem::build(auth::AuthError::InvalidCredentials));
         }

         future::ok(Some((jwt.unwrap(), query.csrf.unwrap())))
       });
```

The glory of the compositional capabilities of `warp` is undescribable.

The `Context` is passed to every GraphQL resolver, and it just bundles
the `Environment` with optionally an authenticated session.

```rust
     let context = warp::any()
       .and(env.clone())
       .and(auth)
       .and_then(|env, auth|
         graphql::Context::new(env, auth).map_err(problem::build))
         .boxed();
```

`juniper` already supports GraphQL subscriptions, so you don't even
need a separate WebSocket for fancier things.

```rust
     let coordinator = Arc::new(Coordinator::new(graphql::schema()));

     let query = warp::post()
       .and(warp::path("query"))
       .and(make_graphql_filter(graphql::schema(), context.clone()));
```

`warp`'s support for WebSockets is already quite neat, and `juniper` just
takes advantage of it.

```rust
     let subscriptions = warp::path("subscriptions")
       .and(warp::ws())
       .and(context.clone())
       .and(warp::any().map(move || Arc::clone(&coordinator)))
       .map(|socket: warp::ws::Ws, context, coordinator|
         socket.on_upgrade(|socket|
           graphql_subscriptions(socket, coordinator, context)
             .map(|res|
               if let Err(err) = res {
                 tracing::error!("websocket error: {:?}", err);
               })
             .boxed()))
       .map(|reply|
         warp::reply::with_header(reply,
           "Sec-WebSocket-Protocol", "graphql-ws"));
```

`juniper` even wraps the GraphQL playground, so you get automagically generated
schema description and documentation and a nice way to test queries out.

```rust
     let playground = warp::path("playground")
       .and(playground_filter("/graphql/query",
         Some("/graphql/subscriptions")));

     warp::path("graphql")
       .and(query.or(subscriptions).or(playground))
  };
```

Just start serving all this stuff, if the response is a rejection
`HttpApiProblem` then it's converted to the proper response, otherwise it's
forwarded through.

```rust
  warp::serve(auth.or(graphql)
    .recover(problem::unpack)
    .with(cors)
    .with(log))
    .run(&args.host).await;

  Ok(())
}
```

Excerpt from the `problem` module.
----------------------------------
Here we turn anything that can turn into an `anyhow::Error` (which is any
`std::Error`) into a `warp::Rejection`.

```rust
pub fn build<E: Into<anyhow::Error>>(err: E) -> Rejection {
  warp::reject::custom(pack(err.into()))
}
```

Here we can turn any internal errors into meaningful responses, or just let
them through as internal server errors.

```rust
pub fn pack(err: anyhow::Error) -> Problem {
  let err = match err.downcast::<Problem>() {
    Ok(problem) =>
      return problem,

    Err(err) =>
      err,
  };

  if let Some(err) = err.downcast_ref::<auth::AuthError>() {
    match err {
      auth::AuthError::InvalidCredentials =>
        return Problem::new("Invalid credentials.")
          .set_status(StatusCode::BAD_REQUEST)
          .set_detail("The passed credentials were invalid."),

      auth::AuthError::ArgonError =>
        ()
    }
  }

  tracing::error!("internal error occurred: {:#}", err);
  Problem::with_title_and_type_from_status(StatusCode::INTERNAL_SERVER_ERROR)
}
```

Here we turn one of our rejections into the proper reply.

```rust
pub async fn unpack(rejection: Rejection) -> Result<impl Reply, Rejection> {
  if let Some(problem) = rejection.find::<Problem>() {
    let code = problem.status
      .unwrap_or(http::StatusCode::INTERNAL_SERVER_ERROR);

    let reply = warp::reply::json(problem);
    let reply = warp::reply::with_status(reply, code);
    let reply = warp::reply::with_header(
      reply,
      http::header::CONTENT_TYPE,
      PROBLEM_JSON_MEDIA_TYPE,
    );

    Ok(reply)
  }
  else {
    Err(rejection)
  }
}
```

Excerpt from the `auth` module.
-------------------------------
Wrap the `Session` model into a newtype so all session actions must happen on
an authenticated session and not on just any session loaded from PostgreSQL.

```rust
#[derive(Shrinkwrap, Clone, Debug)]
pub struct Session(model::Session);

```
This gets automatically deserialized from `warp::body::json()` when stuff is
forwarded to `filter`.

```rust
#[derive(Serialize, Deserialize, Debug)]
pub struct Request {
  email: String,
  password: String,
  lifetime: Option<i64>,
}
```

This stuff is for biscuit to do its JWT thing, `session` stores the session
key (or ID, or whatever you want to call it) and `csrf` is the CSRF token for
the session.

```rust
#[derive(Serialize, Deserialize, Debug, Clone)]
struct Claims {
  session: String,
  csrf: String,
}
```

Comfy error definition using `thiserror`.

```rust
#[derive(Error, Debug)]
pub enum AuthError {
  #[error("invalid credentials")]
  InvalidCredentials,

  #[error("could not hash password")]
  ArgonError,
}
```

This gets forwarded directly from `warp`, it just converts an authentication
request into a `warp::Reply`, the JWT token is set as a cookie, and the JWT and CSRF tokens are returned in the response.

From then on the CSRF token has to be passed as a query parameter, while the
JWT token can be passed as a cookie (usually automagically) or can be passed as
an `Authorization` header.

```rust
pub async fn filter(env: Environment, req: Request, address: Option<SocketAddr>)
  -> anyhow::Result<impl Reply>
{
  let (jwt, csrf) = request(env, req, address).await?;

  let reply = warp::reply::json(&json!({ "jwt": jwt, "csrf": csrf }));
  let reply = warp::reply::with_status(reply, http::StatusCode::OK);

  let reply = warp::reply::with_header(reply,
    http::header::CONTENT_TYPE,
    http_api_problem::PROBLEM_JSON_MEDIA_TYPE);

  let reply = warp::reply::with_header(reply,
    http::header::SET_COOKIE,
    format!("jwt={}", jwt));

  Ok(reply)
}
```

This actually handles the request for a session, checks the user exists and
that the password matches, and if everything goes right creates a new
session.

```rust
pub async fn request(env: Environment, req: Request, addr: Option<SocketAddr>)
  -> anyhow::Result<(String, String)>
{
  let account = query!(r#"
    SELECT id, password
      FROM accounts
      WHERE email = $1
    "#, &req.email)
      .fetch_optional(env.database()).await?
      .ok_or(AuthError::InvalidCredentials)?;

  let is_valid = env.argon().verifier()
    .with_hash(&account.password)
    .with_password(&req.password)
    .verify()
    .or(Err(AuthError::ArgonError))?;

  if !is_valid {
    return Err(AuthError::InvalidCredentials.into());
  }

  let identity = Identity {
    fingerprint: None,
    ip: addr.map(|addr| addr.ip()),
  };

  let claims = Claims {
    session: rand::thread_rng().sample_iter(&Alphanumeric).take(64).collect(),
    csrf: rand::thread_rng().sample_iter(&Alphanumeric).take(64).collect(),
  };

  let csrf = claims.csrf.clone();
  let expiry = Utc::now() + Duration::seconds(env.session_lifetime(req.lifetime));

  query_unchecked!(r#"
    INSERT INTO sessions (key, csrf, account, identity, expiry)
      VALUES ($1, $2, $3, $4, $5)
  "#, &claims.session, &claims.csrf, account.id, Json(identity), expiry)
    .execute(env.database()).await?;

  Ok((env.jwt().encode(claims, expiry)?, csrf))
}
```

This gets an existing session from a JWT token and CSRF token, getting a
session without both is invalid, or if it's expired, or has been explicitly
invalidated. There is no other way to get a `Session` aside from passing
through here.

```rust
pub async fn session(env: Environment, jwt: &str, csrf: &str) -> anyhow::Result<Session> {
  let claims: Claims = env.jwt().decode(jwt)?;

  if claims.csrf != csrf {
    Err(AuthError::InvalidCredentials)?
  }

  let session = query_as_unchecked!(Session, r#"
    SELECT *
      FROM sessions
      WHERE key = $1 AND csrf = $2 AND expiry > NOW() AND NOT invalidated
  "#, claims.session, &csrf)
    .fetch_optional(env.database()).await?;

  Ok(session.ok_or(AuthError::InvalidCredentials)?)
}
```

Excerpts from the `session` module.
-----------------------------------
This `Session` type is basically a glorified type-map backed by Redis using
bincode as serialization format.

The data automagically expires too with the session expiration, breddy cool.

This essentially means API modules can just define the session type they need
and use it transparently without having to worry about anything.

```rust
#[derive(Clone)]
pub struct Session {
  auth: auth::Session,
  env: Environment,
```

A `redis::Client` is not a connection pool, so it's better to eagerly get
a connection that can be cloned with the `Session`.

```rust
  redis: MultiplexedConnection,
}

impl Session {
  pub async fn new(env: Environment, auth: auth::Session) -> anyhow::Result<Self> {
    let redis = env.redis().await?;
    Ok(Self { env, auth, redis })
  }

  pub async fn account(&self) -> anyhow::Result<model::Account> {
    Ok(query_as_unchecked!(model::Account, r#"
      SELECT accounts.*
        FROM sessions
        INNER JOIN accounts
          ON sessions.account = accounts.id
        WHERE
          sessions.key = $1
    "#, self.auth.key)
      .fetch_one(self.env.database()).await?)
  }

  pub async fn set<T: Serialize>(&mut self, value: &T) -> anyhow::Result<()> {
    let expiry = self.auth.expiry.signed_duration_since(Utc::now());

    self.redis.set_ex(
      format!("session:{}:{}", self.auth.key, type_name::<T>()),
      bincode::serialize(value)?, expiry.num_seconds().try_into()?).await?;

    Ok(())
  }

  pub async fn get<T: DeserializeOwned>(&mut self) -> anyhow::Result<T> {
    let bytes: Vec<u8> = self.redis.get(
      format!("session:{}:{}", self.auth.key, type_name::<T>())).await?;

    Ok(bincode::deserialize(&bytes)?)
  }
}
```

Excerpts from the `graphql` modules.
------------------------------------
Bunch of `juniper` boilerplate.

```rust
pub type Schema = juniper::RootNode<'static, Query, Mutation, Subscription>;

pub fn schema() -> Schema {
  Schema::new(Query, Mutation, Subscription)
}

#[derive(Clone)]
pub struct Context {
  session: Option<Session>,
  env: Environment,
}

impl juniper::Context for Context { }
```

Here be all the queries, i.e. read-only things, they can be namespaced but no
need in this case.

```rust
pub struct Query;

#[juniper::graphql_object(Context = Context)]
impl Query {
```

Now this isn't a query that's actually going to be in any system, but it's just
here as an example.

```rust
  pub async fn accounts(ctx: &Context) -> FieldResult<Vec<model::Account>> {
    Ok(query_as_unchecked!(model::Account, "SELECT * FROM accounts")
      .fetch_all(ctx.database())
      .await?)
  }
}
```

Here be all the mutations, i.e. read-write things, it's always better to
namespace these or the generated documentation/schema is impossible to
navigate, and honestly would make it much harder to use as well.

```rust
pub struct Mutation;

#[juniper::graphql_object(Context = Context)]
impl Mutation {
  fn account() -> AccountMutation {
    AccountMutation
  }
}

pub struct AccountMutation;

#[derive(juniper::GraphQLInputObject, Debug)]
pub struct AccountInput {
  email: Option<String>,
}

#[juniper::graphql_object(Context = Context)]
impl AccountMutation {
  async fn update(ctx: &Context, id: Uuid, input: AccountInput)
    -> FieldResult<model::Account>
  {
    let acc = ctx.session().ok_or(auth::InvalidCredentials)?.account().await?;
```

Here if you add a permission/role system you could check for this bullshite.

```rust
    if acc.id != id {
      Err(auth::InvalidCredentials)?
    }

    Ok(query_as_unchecked!(model::Account, r#"
      UPDATE accounts
        SET email = COALESCE($2, email)
        WHERE id = $1
        RETURNING *
    "#, id, input.email)
      .fetch_one(ctx.database()).await?)
  }
}
```

Here be the pub/sub thingy GraphQL provides, creating a stream is going to
still be cumbersome until generators are a thing, but for now I can abuse
channels and spawn a separate future to feed stuff to it.

```rust
pub struct Subscription;

type CallsStream = Pin<Box<dyn Stream<Item = FieldResult<i32>> + Send>>;

#[juniper::graphql_subscription(Context = Context)]
impl Subscription {
  pub async fn calls(ctx: &Context) -> CallsStream {
    let (tx, rx) = channel(16);
    Box::pin(rx)
  }
}
```

Excerpt from the `model` module.
--------------------------------
Just a simple account type, can be expanded to contain roles and permissions
and such.

```rust
#[derive(Clone, Serialize, Deserialize, GraphQLObject, Debug)]
pub struct Account {
  pub id: Uuid,
  pub email: String,

  #[graphql(skip)]
  #[serde(skip_serializing)]
  pub password: Redacted<String>,

  pub created_at: DateTime<Utc>,
  pub updated_at: Option<DateTime<Utc>>,
}
```

This is the structure used by the PostgreSQL database, so far the only issues
I've had with SQLx revolve around user defined types, but they're working on
it, so should be super comfy very soon.

```rust
#[derive(Clone, FromRow, Debug)]
pub struct Session {
  pub key: String,
  pub csrf: String,
  pub account: Uuid,
  pub identity: Json<session::Identity>,
  pub expiry: DateTime<Utc>,
  pub invalidated: bool,

  pub created_at: DateTime<Utc>,
  pub updated_at: Option<DateTime<Utc>>,
}

pub mod session {
  use std::net::IpAddr;
  use serde::{Serialize, Deserialize};
```

This is stored as a JSONB field in the database, SQLx is smart enough to be
able to use custom types just by wrapping the type you want to use with a
`Json<T>` marker.

```rust
  #[derive(Clone, Serialize, Deserialize, Default, Debug)]
  pub struct Identity {
    pub fingerprint: Option<String>,
    pub ip: Option<IpAddr>,
  }
}
```

Now this is a thingy to prevent secretsy things from leaking out via
serialization or printing through logs and such.

When printing it replaces any character with a █ making it look redacted by CIA
agents.

```rust
#[derive(Shrinkwrap, Deserialize, sqlx::Type, Clone, Eq, PartialEq, Ord)]
#[derive(PartialOrd, Hash, Default)]
#[sqlx(transparent)]
pub struct Redacted<T>(T);

impl<T> Redacted<T> {
  pub fn new(value: T) -> Self {
    Self(value)
  }
}

impl<T> Serialize for Redacted<T> {
  fn serialize<S: Serializer>(&self, ser: S) -> Result<S::Ok, S::Error> {
    ser.serialize_none()
  }
}

impl<T: fmt::Debug> fmt::Debug for Redacted<T> {
  fn fmt(&self, f: &mut fmt::Formatter<'_>) -> fmt::Result {
    write!(f, "{}", iter::repeat("█")
      .take(UnicodeWidthStr::width(format!("{:?}", self.0).as_str()))
      .collect::<String>())
  }
}

impl<T: fmt::Display> fmt::Display for Redacted<T> {
  fn fmt(&self, f: &mut fmt::Formatter<'_>) -> fmt::Result {
    write!(f, "{}", iter::repeat("█")
      .take(UnicodeWidthStr::width(format!("{}", self.0).as_str()))
      .collect::<String>())
  }
}
```

{:greentext: style="color: green"}
&gt;me writing web services in Rust in 2020
{:greentext}

![Comfy Crab in Rusty Can](/img/crab-in-a-can.jpg)
