---
title: Of ƒlow and functional programming
categories: [programming, flow]
layout: post
---

In the past few days I've been working on [ƒlow][1], I chose to do it in Erlang
for various reasons.

  1. the find by boolean expression is pretty hard to do and no SQL or NoSQL
     solutions satisfied me.

  2. Erlang scales pretty well and the kind of concurrency I would get for free
     with it would be kind of hard to get with other solutions.

  3. Because I never used Erlang or any functional programming language for
     anything serious.

After getting some hints on how to approach the filtering, I managed to write
some, in my opinion, beautifully concise filtering code, just look at that
`in_expression` function.

```erlang
find_flows(Expression) ->
  MatchSpec = match_all(#flow_float{name = '$1', flows = '$2', _ = '_'},
                        boolean_parser:elements(Expression), {{'$1', '$2'}}),

  case mnesia:transaction(fun() -> mnesia:select(flow_float, MatchSpec) end) of
    {atomic, F} -> {atomic, filter_flows(Expression, dict:from_list(F))};
    Error       -> Error
  end.

filter_flows(Expression, Floats) ->
  {ok, ParsedExpression} = boolean_parser:expression(Expression),
  Flows                  = lists:usort(dict:fold(fun(_, Value, Acc) ->
            Value ++ Acc end, [], Floats)),

  filter_flows(ParsedExpression, Floats, Flows).

% the Expression was just a single float, return early
filter_flows(ParsedExpression, _, Flows) when is_list(ParsedExpression) ->
  Flows;

filter_flows(ParsedExpression, Floats, Flows) ->
  lists:filter(fun(Flow) -> in_expression(Flow, ParsedExpression, Floats) end, Flows).

in_expression(Flow, {'not', What}, Floats) ->
  not in_expression(Flow, What, Floats);

in_expression(Flow, {'and', Left, Right}, Floats) ->
  in_expression(Flow, Left, Floats) and in_expression(Flow, Right, Floats);

in_expression(Flow, {'or', Left, Right}, Floats) ->
  in_expression(Flow, Left, Floats) or in_expression(Flow, Right, Floats);

in_expression(Flow, {'xor', Left, Right}, Floats) ->
  in_expression(Flow, Left, Floats) xor in_expression(Flow, Right, Floats);

% Term is a string, check if the Flow has that float
in_expression(Flow, Term, Floats) when is_list(Term) ->
  lists:member(Flow, dict:fetch(Term, Floats)).
```

The funny part about all this is that I knew what kind of data structure to
return from the parsing, but had no idea how to write `in_expression` at the
time.

[1]: https://github.com/meh/flow
