# What is CodeIgniter

[![CI Status](https://img.shields.io/github/actions/workflow/status/hunxa/codeigniter-3/ci.yml?branch=main&label=CI&logo=github)](https://github.com/hunxa/codeigniter-3/actions)
[![Latest Release](https://img.shields.io/github/v/release/hunxa/codeigniter-3?label=Release&logo=github)](https://github.com/hunxa/codeigniter-3/releases)
[![License](https://img.shields.io/github/license/hunxa/codeigniter-3?label=License)](https://github.com/hunxa/codeigniter-3/blob/main/license.txt)
[![GitHub Stars](https://img.shields.io/github/stars/hunxa/codeigniter-3?style=social)](https://github.com/hunxa/codeigniter-3/stargazers)
[![GitHub Forks](https://img.shields.io/github/forks/hunxa/codeigniter-3?style=social)](https://github.com/hunxa/codeigniter-3/network/members)
[![GitHub Issues](https://img.shields.io/github/issues/hunxa/codeigniter-3?label=Issues)](https://github.com/hunxa/codeigniter-3/issues)
[![GitHub Pull Requests](https://img.shields.io/github/issues-pr/hunxa/codeigniter-3?label=Pull%20Requests)](https://github.com/hunxa/codeigniter-3/pulls)
[![Last Commit](https://img.shields.io/github/last-commit/hunxa/codeigniter-3?label=Last%20Commit)](https://github.com/hunxa/codeigniter-3/commits/main)
[![Contributors](https://img.shields.io/github/contributors/hunxa/codeigniter-3?label=Contributors)](https://github.com/hunxa/codeigniter-3/graphs/contributors)
[![PHP Version](https://img.shields.io/badge/PHP-%3E%3D%205.6-blue?logo=php)](https://www.php.net/)
[![CodeIgniter Version](https://img.shields.io/badge/CodeIgniter-3.x-orange?logo=codeigniter)](https://codeigniter.com/)

CodeIgniter is an Application Development Framework - a toolkit - for people
who build web sites using PHP. Its goal is to enable you to develop projects
much faster than you could if you were writing code from scratch, by providing
a rich set of libraries for commonly needed tasks, as well as a simple
interface and logical structure to access these libraries. CodeIgniter lets
you creatively focus on your project by minimizing the amount of code needed
for a given task.

## CodeIgniter 3

This repository is a maintained fork of CodeIgniter 3, the legacy version of the framework.

[CodeIgniter 4](https://github.com/codeigniter4/CodeIgniter4) is the latest
version of the framework, while this repository focuses on maintaining and improving
CodeIgniter 3 for projects that require PHP 5.6+ compatibility.

CodeIgniter 3 is intended for use with PHP 5.6+ and continues to receive security
updates and community improvements through this repository.

## Release Information

This repo contains the maintained version of CodeIgniter 3. For the official releases,
please visit the [CodeIgniter Downloads](https://codeigniter.com/download) page.

For this fork's releases and updates, check the [Releases section](https://github.com/hunxa/codeigniter-3/releases).

## Changelog and New Features

You can find a list of all changes for each release in the [user guide change log](https://github.com/bcit-ci/CodeIgniter/blob/develop/user_guide_src/source/changelog.rst).

For changes specific to this fork, see the [CHANGELOG.md](https://github.com/hunxa/codeigniter-3/blob/main/CHANGELOG.md) file.

## Server Requirements

PHP version 5.6 or newer is recommended.

It should work on 5.4.8 as well, but we strongly advise you NOT to run
such old versions of PHP, because of potential security and performance
issues, as well as missing features.

## Installation

### Via Composer

```bash
composer create-project hunxa/codeigniter-3 your-project-name
```

### Via Git Clone

```bash
git clone https://github.com/hunxa/codeigniter-3.git your-project-name
cd your-project-name
```

### Manual Download

Download the latest release from the [Releases page](https://github.com/hunxa/codeigniter-3/releases)
and extract to your web server directory.

For detailed installation instructions, see the [installation section](https://codeigniter.com/userguide3/installation/index.html)
of the CodeIgniter User Guide.

## CI/CD Integration

This repository includes GitHub Actions workflows for continuous integration:

- Automatic PHP syntax validation
- CodeIgniter structure verification
- Security checks
- Code quality analysis

The CI pipeline runs automatically on every push to the main branch and on pull requests.

## License

Please see the [license agreement](https://github.com/bcit-ci/CodeIgniter/blob/develop/user_guide_src/source/license.rst).

## Resources

- [User Guide](https://codeigniter.com/userguide3/)
- [Contributing Guide](https://github.com/hunxa/codeigniter-3/blob/main/CONTRIBUTING.md)
- [Language File Translations](https://github.com/bcit-ci/codeigniter3-translations)
- [Community Forums](https://forum.codeigniter.com/)
- [Community Wiki](https://github.com/bcit-ci/CodeIgniter/wiki)
- [Community Slack Channel](https://codeigniterchat.slack.com)

## Contributing

We welcome contributions! Please read our [Contributing Guide](https://github.com/hunxa/codeigniter-3/blob/main/CONTRIBUTING.md)
before submitting pull requests.

### Reporting Issues

- For security issues, please report privately to the repository maintainer
- For bugs and features, open an issue on [GitHub Issues](https://github.com/hunxa/codeigniter-3/issues)

## Security

Report security issues to the repository maintainer or via our
[Security page](https://github.com/hunxa/codeigniter-3/security).

For official CodeIgniter security issues, contact the [Security Panel](mailto:security@codeigniter.com)
or via the [HackerOne page](https://hackerone.com/codeigniter).

## Acknowledgement

The CodeIgniter team would like to thank EllisLab, all the
contributors to the CodeIgniter project, the original CodeIgniter team,
and you, the CodeIgniter user.

This fork is maintained by the community to keep CodeIgniter 3 alive and secure
for projects that continue to rely on it.

---

**Repository:** https://github.com/hunxa/codeigniter-3  
**Original Repository:** https://github.com/bcit-ci/CodeIgniter  
**Maintained by:** hunxa
