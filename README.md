# Consent Wow WordPress Plugin

PDPA-compliant consent management for your web forms.

A WordPress plugin for consent management via [Consent Wow](https://consentwow.com).

## Prerequisites

Before using the plugin, please [sign up a Consent Wow account](https://app.consentwow.com/sign-up),
set up a project, and add consent source to start collect your customer's
consent.

## Installation

1. Download the plugin.
2. Zip /trunk folder (or another version in /tags/*version*) and name it
   `consent-wow-consent-solution.zip`.
3. Upload zip file into your WordPress.
4. Activate the plugin.
5. Set an API Key from Consent Wow.
6. Add Form mapping.

## License

Released under the terms of the <abbr>GPL</abbr> (GNU General Public License)
version 3 or any later version. See <a href="trunk/LICENSE">LICENSE</a> for
more information.

## Contact

support@consentwow.com

# Development

WordPress Plugin use Subversion as VCS, so pushing codes to Git will not
directly update the plugin.

However, WordPress says [that](https://developer.wordpress.org/plugins/wordpress-org/how-to-use-subversion/)
```
Warning:SVN and the Plugin Directory are a release repository. Unlike Git, you
shouldn’t commit every small change, as doing so can degrade performance. Please
only push finished changes to your SVN repository.
```
Therefore, We will use Git for development and SVN for deployment.

## Folder Structure

The current version (and development version) of code should be inside /trunk
folder.

After finishing the new version, create a new tag by copy /trunk to
/tags/*version* and also create a tag via Git.

# Deployment

In order to deploy the new version, you should follow the
[instructions from WordPress](https://developer.wordpress.org/plugins/wordpress-org/how-to-use-subversion/).

Please note that we [should not push any irrelevant code to SVN repo](https://developer.wordpress.org/plugins/wordpress-org/how-to-use-subversion/#notes)
, e.g. .gitignore or README.md, so we need to ignore some files before commit
our code.

Therefore, we have create a text file "svnignore.txt" to store the filepaths we
want to ignore on SVN.

Run the command below before committing your code via SVN.
```
svn propset svn:ignore -F svnignore.txt .
```
