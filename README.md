# maginium-starter

[![maginium-starter](https://i.ibb.co/ZGy9pMX/github-banner.png)](https://github.com/maginium/template)

Ready to use empty maginium starter project template. It has a simple and clean project structure with many features for development and debugging. When you decide to won't use some tools you can remove them quickly, then you can continue to develop.

## Installation

Create the project with composer:

```bash
composer create-project maginium/template {project_name} --stability=dev
```

> [!WARNING]
> After installation, if you are using VS Code editor CLI, you need to restart with the `F1` (or `Command` + `P`, or `fn` + `F1`) > `Reload Window` command. This is required for indexing and plugin activation.

## To sync with template repo

Run this command

```
git remote add template https://github.com/maginium/template
git fetch --all
git merge template/main --allow-unrelated-histories
```

## Documentation

You can find the documentation **[here](https://pixiedia.gitbook.io/maginium).**
