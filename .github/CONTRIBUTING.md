# Contributing

Contributions are **welcome** and will be credited.

We accept contributions via Pull Requests on [Github](https://github.com/omines/kameleon).
Do [PHP the Right Way](http://www.phptherightway.com/), keep the PHPstan level maxed, include tests with proper
coverage, and run `bin/prepare-commit` during development and before committing.

Infection testing is by default only done on **changed** files. It is *recommended* to run
`bin/infection` before finishing a PR to evaluate infection status of the entire project.

## Update documentation

To serve the MkDocs site in `/docs` install dependencies:

```sh
pip install mkdocs mkdocs-material mkdocs-material-extensions

Then serve the docs with live updates:

```sh
bin/serve-docs
```

By default MkDocs binds to port 8000 and is thus accessible on http://127.0.0.1:8000. If you get a port
conflict, append the `-a` parameter for custom binding:

```shell
# Bind to localhost port 4000
bin/serve-docs -a 127.0.0.1:4000
```
On pushes to the `master` branch the docs are automatically released to https://omines.github.io/kameleon/