Azure Blob CLI is a tool that makes managing Azure Blobs and Containers on the fly easy. It is an **unofficial** and prunned version of azure-cli built ontop of Laravel Zero. 

To learn more about laravel zero, visit [link](http://laravel-zero.com/).

------

## Screenshots

<img src="https://i.imgur.com/hFdcUUb.gif" title="source: imgur.com" />

## Getting Started

To make this a cinch, add your Azure Account Name and Storage Key as `AZURE_STORAGE_NAME` and `AZURE_STORAGE_KEY` values in the .env.sample file. Then rename .env.sample to .env.

### Command Syntax

```
php azure-blob-cli {command}
```
## Features

- List Containers
  ```
  php azure-blob-cli containers
  ```
- Create Container
  ```
  php azure-blob-cli create-container {container-name}
  ```
- Delete Container
  ```
  php azure-blob-cli delete-container {container-name}
  ```
- Download Blobs
  ```
  php azure-blob-cli explore {container-name}
  ```
## License

Azure Blob Cli is an open-source software licensed under the [MIT license](https://github.com/emugabi/azure-blob-cli/blob/stable/LICENSE.md).
