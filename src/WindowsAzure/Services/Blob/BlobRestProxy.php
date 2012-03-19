<?php

/**
 * LICENSE: Licensed under the Apache License, Version 2.0 (the "License")
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * 
 * PHP version 5
 *
 * @category  Microsoft
 * @package   PEAR2\WindowsAzure\Services\Blob\BlobRestProxy
 * @author    Abdelrahman Elogeel <Abdelrahman.Elogeel@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @link      http://pear.php.net/package/azure-sdk-for-php
 */
 
namespace PEAR2\WindowsAzure\Services\Blob;
use PEAR2\WindowsAzure\Utilities;
use PEAR2\WindowsAzure\Resources;
use PEAR2\WindowsAzure\Core\WindowsAzureUtilities;
use PEAR2\WindowsAzure\Services\Core\ServiceRestProxy;
use PEAR2\WindowsAzure\Services\Blob\IBlob;
use PEAR2\WindowsAzure\Services\Blob\Models\BlobServiceOptions;
use PEAR2\WindowsAzure\Services\Core\Models\GetServicePropertiesResult;
use PEAR2\WindowsAzure\Services\Blob\Models\ListContainersOptions;
use PEAR2\WindowsAzure\Services\Blob\Models\ListContainersResult;
use PEAR2\WindowsAzure\Services\Blob\Models\CreateContainerOptions;
use PEAR2\WindowsAzure\Services\Blob\Models\GetContainerPropertiesResult;
use PEAR2\WindowsAzure\Services\Blob\Models\GetContainerACLResult;
use PEAR2\WindowsAzure\Services\Blob\Models\SetContainerMetadataOptions;
use PEAR2\WindowsAzure\Services\Blob\Models\ListBlobsOptions;
use PEAR2\WindowsAzure\Services\Blob\Models\ListBlobsResult;
use PEAR2\WindowsAzure\Services\Blob\Models\BlobType;
use PEAR2\WindowsAzure\Services\Blob\Models\CreateBlobOptions;

/**
 * This class constructs HTTP requests and receive HTTP responses for blob
 * service layer.
 *
 * @category  Microsoft
 * @package   PEAR2\WindowsAzure\Services\Blob\BlobRestProxy
 * @author    Abdelrahman Elogeel <Abdelrahman.Elogeel@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/azure-sdk-for-php
 */
class BlobRestProxy extends ServiceRestProxy implements IBlob
{
    /**
     * Helper method for getContainerProperties and getContainerMetadata
     * 
     * @param string             $container name
     * @param BlobServiceOptions $options   optional parameters
     * @param string             $operation should be 'metadata' to get metadata
     * 
     * @return GetContainerPropertiesResult
     */
    private function _getContainerPropertiesImpl($container, $options = null,
        $operation = null
    ) {
        $method      = \HTTP_Request2::METHOD_GET;
        $headers     = array();
        $queryParams = array();
        $path        = $container;
        $statusCode  = Resources::STATUS_OK;
        
        if (is_null($options)) {
            $options = new BlobServiceOptions();
        }
        
        $queryParams['restype']             = 'container';
        $queryParams[Resources::QP_COMP]    = $operation;
        $queryParams[Resources::QP_TIMEOUT] = strval($options->getTimeout());
        
        $response = $this->send($method, $headers, $queryParams, $path, $statusCode);
        $result   = new GetContainerPropertiesResult();
        $metadata = WindowsAzureUtilities::getMetadataArray($response->getHeader());
        $date     = $response->getHeader(Resources::LAST_MODIFIED);
        $date     = WindowsAzureUtilities::rfc1123ToDateTime($date);
        $result->setEtag($response->getHeader(Resources::ETAG));
        $result->setMetadata($metadata);
        $result->setLastModified($date);
        
        return $result;
    }
    
    /**
     * Adds optional create blob headers.
     * 
     * @param CreateBlobOptions $options optional parameters
     * @param array             $headers request headers
     * 
     * @return array
     */
    private function _addCreateBlobOptionalHeaders($options, $headers)
    {
        $contentType         = $options->getContentType();
        $metadata            = $options->getMetadata();
        $accessCondition     = $options->getAccessCondition();
        $blobContentType     = $options->getBlobContentType();
        $blobContentEncoding = $options->getBlobContentEncoding();
        $blobContentLanguage = $options->getBlobContentLanguage();
        $blobContentMD5      = $options->getBlobContentMD5();
        $blobCacheControl    = $options->getBlobCacheControl();
        $leaseId             = $options->getLeaseId();
        
        if (!is_null($contentType)) {
            $headers[Resources::CONTENT_TYPE] = $options->getContentType();
        } else {
            $headers[Resources::CONTENT_TYPE] = Resources::BINARY_FILE_TYPE;
        }
        
        if (!is_null($metadata) && !empty($metadata)) {
            $metadata = WindowsAzureUtilities::generateMetadataHeaders($metadata);
            $headers  = array_merge($headers, $metadata);
        }
        
        $this->addOptionalAccessContitionHeader($headers, $accessCondition);
        
        $headers[Resources::CONTENT_ENCODING] = $options->getContentEncoding();
        $headers[Resources::CONTENT_LANGUAGE] = $options->getContentLanguage();
        $headers[Resources::CONTENT_MD5]      = $options->getContentMD5();
        $headers[Resources::CACHE_CONTROL]    = $options->getCacheControl();
        $headers[Resources::X_MS_LEASE_ID]    = $leaseId;
        
        $headers[Resources::X_MS_BLOB_CONTENT_TYPE]     = $blobContentType;
        $headers[Resources::X_MS_BLOB_CONTENT_ENCODING] = $blobContentEncoding;
        $headers[Resources::X_MS_BLOB_CONTENT_LANGUAGE] = $blobContentLanguage;
        $headers[Resources::X_MS_BLOB_CONTENT_MD5]      = $blobContentMD5;
        $headers[Resources::X_MS_BLOB_CACHE_CONTROL]    = $blobCacheControl;
        
        return $headers;
    }
    
    /**
     * Gets the properties of the Blob service.
     * 
     * @param Models\BlobServiceOptions $options optional blob service options.
     * 
     * @return PEAR2\WindowsAzure\Services\Core\Models\GetServicePropertiesResult
     * 
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/hh452239.aspx
     */
    public function getServiceProperties($options = null)
    {
        $method      = \HTTP_Request2::METHOD_GET;
        $headers     = array();
        $queryParams = array();
        $path        = Resources::EMPTY_STRING;
        $statusCode  = Resources::STATUS_OK;
        
        if (is_null($options)) {
            $options = new BlobServiceOptions();
        }
        
        $queryParams['restype']             = 'service';
        $queryParams[Resources::QP_COMP]    = 'properties';
        $queryParams[Resources::QP_TIMEOUT] = strval($options->getTimeout());
        
        $response = $this->send($method, $headers, $queryParams, $path, $statusCode);
        $parsed   = Utilities::unserialize($response->getBody());
        
        return GetServicePropertiesResult::create($parsed);
    }

    /**
     * Sets the properties of the Blob service.
     * 
     * @param ServiceProperties         $serviceProperties new service properties.
     * @param Models\BlobServiceOptions $options           optional parameters
     * 
     * @return none.
     * 
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/hh452235.aspx
     */
    public function setServiceProperties($serviceProperties, $options = null)
    {
        $method      = \HTTP_Request2::METHOD_PUT;
        $headers     = array();
        $queryParams = array();
        $statusCode  = Resources::STATUS_ACCEPTED;
        $path        = Resources::EMPTY_STRING;
        $body        = Resources::EMPTY_STRING;
        
        if (!isset($options)) {
            $options = new BlobServiceOptions();
        }
        
        $queryParams['restype']             = 'service';
        $queryParams[Resources::QP_COMP]    = 'properties';
        $queryParams[Resources::QP_TIMEOUT] = strval($options->getTimeout());
        $body                               = $serviceProperties->toXml();
        $headers[Resources::CONTENT_TYPE]   = Resources::XML_CONTENT_TYPE;
        
        $this->send($method, $headers, $queryParams, $path, $statusCode, $body);
    }
    
    /**
     * Lists all of the containers in the given storage account.
     * 
     * @param Models\ListContainersOptions $options optional parameters
     * 
     * @return PEAR2\WindowsAzure\Services\Blob\Models\ListContainersResult
     * 
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd179352.aspx
     */
    public function listContainers($options = null)
    {
        $method      = \HTTP_Request2::METHOD_GET;
        $headers     = array();
        $queryParams = array();
        $path        = Resources::EMPTY_STRING;
        $statusCode  = Resources::STATUS_OK;
        
        if (is_null($options)) {
            $options = new ListContainersOptions();
        }
        
        $queryParams[Resources::QP_TIMEOUT]     = strval($options->getTimeout());
        $queryParams[Resources::QP_COMP]        = 'list';
        $queryParams[Resources::QP_PREFIX]      = $options->getPrefix();
        $queryParams[Resources::QP_MARKER]      = $options->getMarker();
        $queryParams[Resources::QP_MAX_RESULTS] = $options->getMaxResults();
        $isInclude                              = $options->getIncludeMetadata();
        $queryParams[Resources::QP_INCLUDE]     = $isInclude ? 'metadata' : null;
        
        $response = $this->send($method, $headers, $queryParams, $path, $statusCode);
        $parsed   = Utilities::unserialize($response->getBody());
        
        return ListContainersResult::create($parsed);
    }
    
    /**
     * Creates a new container in the given storage account.
     * 
     * @param string                        $container name
     * @param Models\CreateContainerOptions $options   optional parameters
     * 
     * @return none.
     * 
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd179468.aspx
     */
    public function createContainer($container, $options = null)
    {
        $method      = \HTTP_Request2::METHOD_PUT;
        $headers     = array();
        $queryParams = array('restype' => 'container');
        $path        = $container;
        $statusCode  = Resources::STATUS_CREATED;
        
        if (is_null($options)) {
            $options = new CreateContainerOptions();
        }

        $queryParams[Resources::QP_TIMEOUT] = strval($options->getTimeout());
        
        $metadataHeaders = WindowsAzureUtilities::generateMetadataHeaders(
            $options->getMetadata()
        );
        
        $headers                                     = $metadataHeaders;
        $headers[Resources::X_MS_BLOB_PUBLIC_ACCESS] = $options->getPublicAccess();
        
        $this->send($method, $headers, $queryParams, $path, $statusCode);
    }
    
    /**
     * Creates a new container in the given storage account.
     * 
     * @param string                    $container name of the container
     * @param Models\BlobServiceOptions $options   optional parameters
     * 
     * @return none.
     * 
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd179408.aspx
     */
    public function deleteContainer($container, $options = null)
    {
        $method      = \HTTP_Request2::METHOD_DELETE;
        $headers     = array();
        $queryParams = array();
        $path        = $container;
        $statusCode  = Resources::STATUS_ACCEPTED;
        
        if (is_null($options)) {
            $options = new BlobServiceOptions();
        }
        
        $queryParams['restype']             = 'container';
        $queryParams[Resources::QP_TIMEOUT] = strval($options->getTimeout());
        
        $this->send($method, $headers, $queryParams, $path, $statusCode);
    }
    
    /**
     * Returns all properties and metadata on the container.
     * 
     * @param string                    $container name
     * @param Models\BlobServiceOptions $options   optional parameters
     * 
     * @return Models\GetContainerPropertiesResult
     * 
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd179370.aspx
     */
    public function getContainerProperties($container, $options = null)
    {
        return $this->_getContainerPropertiesImpl($container, $options);
    }
    
    /**
     * Returns only user-defined metadata for the specified container.
     * 
     * @param string                    $container name
     * @param Models\BlobServiceOptions $options   optional parameters
     * 
     * @return Models\GetContainerPropertiesResult
     * 
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/ee691976.aspx 
     */
    public function getContainerMetadata($container, $options = null)
    {
        return $this->_getContainerPropertiesImpl($container, $options, 'metadata');
    }
    
    /**
     * Gets the access control list (ACL) and any container-level access policies 
     * for the container.
     * 
     * @param string                    $container name
     * @param Models\BlobServiceOptions $options   optional parameters
     * 
     * @return Models\GetContainerACLResult
     * 
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd179469.aspx
     */
    public function getContainerACL($container, $options = null)
    {
        $method      = \HTTP_Request2::METHOD_GET;
        $headers     = array();
        $queryParams = array();
        $path        = $container;
        $statusCode  = Resources::STATUS_OK;
        
        if (is_null($options)) {
            $options = new BlobServiceOptions();
        }
        
        $queryParams[Resources::QP_TIMEOUT] = strval($options->getTimeout());
        $queryParams['restype']             = 'container';
        $queryParams[Resources::QP_COMP]    = 'acl';
        
        $response = $this->send($method, $headers, $queryParams, $path, $statusCode);
        
        $access       = $response->getHeader(Resources::X_MS_BLOB_PUBLIC_ACCESS);
        $etag         = $response->getHeader(Resources::ETAG);
        $lastModified = $response->getHeader(Resources::LAST_MODIFIED);
        $parsed       = Utilities::unserialize($response->getBody());
                
        return GetContainerACLResult::create($access, $etag, $lastModified, $parsed);
    }
    
    /**
     * Sets the ACL and any container-level access policies for the container.
     * 
     * @param string                    $container name
     * @param Models\ContainerACL       $acl       access control list for container
     * @param Models\BlobServiceOptions $options   optional parameters
     * 
     * @return none.
     * 
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd179391.aspx
     */
    public function setContainerACL($container, $acl, $options = null)
    {
        $method      = \HTTP_Request2::METHOD_PUT;
        $headers     = array();
        $queryParams = array();
        $path        = $container;
        $statusCode  = Resources::STATUS_OK;
        $body        = $acl->toXml();
        
        if (is_null($options)) {
            $options = new BlobServiceOptions();
        }
        
        $queryParams[Resources::QP_TIMEOUT] = strval($options->getTimeout());
        $queryParams['restype']             = 'container';
        $queryParams[Resources::QP_COMP]    = 'acl';
        
        $headers[Resources::X_MS_BLOB_PUBLIC_ACCESS] = $acl->getPublicAccess();
        $headers[Resources::CONTENT_TYPE]            = Resources::XML_CONTENT_TYPE;

        $this->send($method, $headers, $queryParams, $path, $statusCode, $body);
    }
    
    /**
     * Sets metadata headers on the container.
     * 
     * @param string                             $container name
     * @param array                              $metadata  metadata key/value pair.
     * @param Models\SetContainerMetadataOptions $options   optional parameters
     * 
     * @return none.
     * 
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd179362.aspx
     */
    public function setContainerMetadata($container, $metadata, $options = null)
    {
        $method      = \HTTP_Request2::METHOD_PUT;
        $headers     = WindowsAzureUtilities::generateMetadataHeaders($metadata);
        $queryParams = array();
        $path        = $container;
        $statusCode  = Resources::STATUS_OK;
        
        if (is_null($options)) {
            $options = new SetContainerMetadataOptions();
        }
        
        $queryParams[Resources::QP_TIMEOUT] = strval($options->getTimeout());
        $queryParams['restype']             = 'container';
        $queryParams[Resources::QP_COMP]    = 'metadata';
        
        $header           = $options->getAccessCondition()->getHeader();
        $value            = $options->getAccessCondition()->getValue();
        $headers[$header] = $value;

        $this->send($method, $headers, $queryParams, $path, $statusCode);
    }
    
    /**
     * Lists all of the blobs in the given container.
     * 
     * @param string                  $container name
     * @param Models\ListBlobsOptions $options   optional parameters
     * 
     * @return Models\ListBlobsResult
     * 
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd135734.aspx
     */
    public function listBlobs($container, $options = null)
    {
        $method      = \HTTP_Request2::METHOD_GET;
        $headers     = array();
        $queryParams = array();
        $path        = $container;
        $statusCode  = Resources::STATUS_OK;
        
        if (is_null($options)) {
            $options = new ListBlobsOptions();
        }
        
        $queryParams[Resources::QP_TIMEOUT]     = strval($options->getTimeout());
        $queryParams[Resources::QP_COMP]        = 'list';
        $queryParams['restype']                 = 'container';
        $queryParams[Resources::QP_PREFIX]      = $options->getPrefix();
        $queryParams[Resources::QP_MARKER]      = $options->getMarker();
        $queryParams[Resources::QP_DELIMITER]   = $options->getDelimiter();
        $queryParams[Resources::QP_MAX_RESULTS] = strval($options->getMaxResults());
        
        $includeMetadata         = $options->getIncludeMetadata();
        $includeSnapshots        = $options->getIncludeSnapshots();
        $includeUncommittedBlobs = $options->getIncludeUncommittedBlobs();
        
        $includeValue = $this->groupQueryValues(
            array(
                $includeMetadata ? 'metadata' : null, 
                $includeSnapshots ? 'snapshots' : null, 
                $includeUncommittedBlobs ? 'uncommittedblobs' : null
            )
        );
        
        $queryParams[Resources::QP_INCLUDE] = $includeValue;
        
        $response = $this->send($method, $headers, $queryParams, $path, $statusCode);
        $parsed   = Utilities::unserialize($response->getBody());
        
        return ListBlobsResult::create($parsed);
    }
    
    /**
     * Creates a new page blob. Note that calling createPageBlob to create a page
     * blob only initializes the blob.
     * To add content to a page blob, call createBlobPages method.
     * 
     * @param string                   $container name of the container
     * @param string                   $blob      name of the blob
     * @param integer                  $length    specifies the maximum size for the
     * page blob, up to 1 TB. The page blob size must be aligned to a 512-byte 
     * boundary.
     * @param Models\CreateBlobOptions $options   optional parameters
     * 
     * @return none.
     * 
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd179451.aspx
     */
    public function createPageBlob($container, $blob, $length, $options = null)
    {
        $method      = \HTTP_Request2::METHOD_PUT;
        $headers     = array();
        $queryParams = array();
        $path        = $container . '/' . $blob;
        $statusCode  = Resources::STATUS_CREATED;
        
        if (is_null($options)) {
            $options = new CreateBlobOptions();
        }
        
        $sequenceNumber = strval($options->getSequenceNumber());
        
        $headers[Resources::X_MS_BLOB_TYPE]            = BlobType::PAGE_BLOB;
        $headers[Resources::X_MS_BLOB_CONTENT_LENGTH]  = strval($length);
        $headers[Resources::X_MS_BLOB_SEQUENCE_NUMBER] = $sequenceNumber;
        
        $headers = $this->_addCreateBlobOptionalHeaders($options, $headers);
        
        $queryParams[Resources::QP_TIMEOUT] = strval($options->getTimeout());
        
        $this->send($method, $headers, $queryParams, $path, $statusCode);
    }
    
    /**
     * Creates a new block blob or updates the content of an existing block blob.
     * Updating an existing block blob overwrites any existing metadata on the blob.
     * Partial updates are not supported with createBlockBlob the content of the
     * existing blob is overwritten with the content of the new blob. To perform a
     * partial update of the content of a block blob, use the createBlockList method.
     * 
     * @param string                   $container name of the container
     * @param string                   $blob      name of the blob
     * @param string                   $content   content of the blob
     * @param Models\CreateBlobOptions $options   optional parameters
     * 
     * @return none.
     * 
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd179451.aspx
     */
    public function createBlockBlob($container, $blob, $content, $options = null)
    {
        throw new \Exception(Resources::NOT_IMPLEMENTED_MSG);
    }
    
    /**
     * Clears a range of pages from the blob.
     * 
     * @param string                        $container name of the container
     * @param string                        $blob      name of the blob
     * @param Models\PageRange              $range     Can be up to the value of the
     * blob's full size.
     * @param Models\CreateBlobPagesOptions $options   optional parameters
     * 
     * @return Models\CreateBlobPagesResult.
     * 
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/ee691975.aspx
     */
    public function clearBlobPages($container, $blob, $range, $options = null)
    {
        throw new \Exception(Resources::NOT_IMPLEMENTED_MSG);
    }
    
    /**
     * Creates a range of pages to a page blob.
     * 
     * @param string                        $container name of the container
     * @param string                        $blob      name of the blob
     * @param Models\PageRange              $range     Can be up to 4 MB in size
     * @param integer                       $length    specifies the maximum size for
     * the page blob, up to 1 TB. The page blob size must be aligned to a 512-byte 
     * boundary.
     * @param string                        $content   the blob contents.
     * @param Models\CreateBlobPagesOptions $options   optional parameters
     * 
     * @return Models\CreateBlobPagesResult.
     * 
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/ee691975.aspx
     */
    public function createBlobPages($container, $blob, $range, $length, $content,
        $options = null
    ) {
        throw new \Exception(Resources::NOT_IMPLEMENTED_MSG);
    }
    
    /**
     * Creates a new block to be committed as part of a block blob.
     * 
     * @param string                        $container name of the container
     * @param string                        $blob      name of the blob
     * @param string                        $blockId   must be less than or equal to 
     * 64 bytes in size. For a given blob, the length of the value specified for the
     * blockid parameter must be the same size for each block.
     * @param string                        $content   the blob block contents
     * @param Models\CreateBlobBlockOptions $options   optional parameters
     * 
     * @return none.
     * 
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd135726.aspx
     */
    public function createBlobBlock($container, $blob, $blockId, $content,
        $options = null
    ) {
        throw new \Exception(Resources::NOT_IMPLEMENTED_MSG);
    }
    
    /**
     * This method writes a blob by specifying the list of block IDs that make up the
     * blob. In order to be written as part of a blob, a block must have been 
     * successfully written to the server in a prior createBlobBlock method.
     * 
     * You can call Put Block List to update a blob by uploading only those blocks 
     * that have changed, then committing the new and existing blocks together. 
     * You can do this by specifying whether to commit a block from the committed 
     * block list or from the uncommitted block list, or to commit the most recently
     * uploaded version of the block, whichever list it may belong to.
     * 
     * @param string                         $container name of the container
     * @param string                         $blob      name of the blob
     * @param Models\BlockList               $blockList the block list entries
     * @param Models\CommitBlobBlocksOptions $options   optional parameters
     * 
     * @return none.
     * 
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd179467.aspx 
     */
    public function commitBlobBlocks($container, $blob, $blockList, $options = null)
    {
        throw new \Exception(Resources::NOT_IMPLEMENTED_MSG);
    }
    
    /**
     * Retrieves the list of blocks that have been uploaded as part of a block blob.
     * 
     * There are two block lists maintained for a blob:
     * 1) Committed Block List: The list of blocks that have been successfully 
     *    committed to a given blob with commitBlobBlocks.
     * 2) Uncommitted Block List: The list of blocks that have been uploaded for a 
     *    blob using Put Block (REST API), but that have not yet been committed. 
     *    These blocks are stored in Windows Azure in association with a blob, but do
     *    not yet form part of the blob.
     * 
     * @param string                       $container name of the container
     * @param string                       $blob      name of the blob
     * @param Models\ListBlobBlocksOptions $options   optional parameters
     * 
     * @return Models\ListBlobBlocksResult
     * 
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd179400.aspx
     */
    public function listBlobBlocks($container, $blob, $options = null)
    {
        throw new \Exception(Resources::NOT_IMPLEMENTED_MSG);
    }
    
    /**
     * Returns all properties and metadata on the blob.
     * 
     * @param string                          $container name of the container
     * @param string                          $blob      name of the blob
     * @param Models\GetBlobPropertiesOptions $options   optional parameters
     * 
     * @return Models\GetBlobPropertiesResult
     * 
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd179394.aspx
     */
    public function getBlobProperties($container, $blob, $options = null)
    {
        throw new \Exception(Resources::NOT_IMPLEMENTED_MSG);
    }
    
    /**
     * Returns all properties and metadata on the blob.
     * 
     * @param string                        $container name of the container
     * @param string                        $blob      name of the blob
     * @param Models\GetBlobMetadataOptions $options   optional parameters
     * 
     * @return Models\GetBlobMetadataResult
     * 
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd179350.aspx
     */
    public function getBlobMetadata($container, $blob, $options = null)
    {
        throw new \Exception(Resources::NOT_IMPLEMENTED_MSG);
    }
    
    /**
     * Returns a list of active page ranges for a page blob. Active page ranges are 
     * those that have been populated with data.
     * 
     * @param string                       $container name of the container
     * @param string                       $blob      name of the blob
     * @param Models\ListBlobRangesOptions $options   optional parameters
     * 
     * @return Models\ListBlobRangesResult
     * 
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/ee691973.aspx
     */
    public function listPageBlobRanges($container, $blob, $options = null)
    {
        throw new \Exception(Resources::NOT_IMPLEMENTED_MSG);
    }
    
    /**
     * Sets system properties defined for a blob.
     * 
     * @param string                          $container name of the container
     * @param string                          $blob      name of the blob
     * @param Models\SetBlobPropertiesOptions $options   optional parameters
     * 
     * @return Models\SetBlobPropertiesResult
     * 
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/ee691966.aspx
     */
    public function setBlobProperties($container, $blob, $options = null)
    {
        throw new \Exception(Resources::NOT_IMPLEMENTED_MSG);
    }
    
    /**
     * Sets metadata headers on the blob.
     * 
     * @param string                        $container name of the container
     * @param string                        $blob      name of the blob
     * @param array                         $metadata  key/value pair representation
     * @param Models\SetBlobMetadataOptions $options   optional parameters
     * 
     * @return Models\SetBlobMetadataResult
     * 
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd179414.aspx
     */
    public function setBlobMetadata($container, $blob, $metadata, $options = null)
    {
        throw new \Exception(Resources::NOT_IMPLEMENTED_MSG);
    }
    
    /**
     * Reads or downloads a blob from the system, including its metadata and 
     * properties.
     * 
     * @param string                $container name of the container
     * @param string                $blob      name of the blob
     * @param Models\GetBlobOptions $options   optional parameters
     * 
     * @return Models\GetBlobResult
     * 
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd179440.aspx
     */
    public function getBlob($container, $blob, $options = null)
    {
        throw new \Exception(Resources::NOT_IMPLEMENTED_MSG);
    }
    
    /**
     * Deletes a blob.
     * 
     * @param string                   $container name of the container
     * @param string                   $blob      name of the blob
     * @param Models\DeleteBlobOptions $options   optional parameters
     * 
     * @return Models\DeleteBlobResult
     * 
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd179413.aspx
     */
    public function deleteBlob($container, $blob, $options = null)
    {
        throw new \Exception(Resources::NOT_IMPLEMENTED_MSG);
    }
    
    /**
     * Creates a snapshot of a blob.
     * 
     * @param string                           $container name of the container
     * @param string                           $blob      name of the blob
     * @param Models\CreateBlobSnapshotOptions $options   optional parameters
     * 
     * @return Models\CreateBlobSnapshotResult
     * 
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/ee691971.aspx
     */
    public function createBlobSnapshot($container, $blob, $options = null)
    {
        throw new \Exception(Resources::NOT_IMPLEMENTED_MSG);
    }
    
    /**
     * Copies a source blob to a destination blob within the same storage account.
     * 
     * @param string                 $destinationContainer name of container
     * @param string                 $destinationBlob      name of blob
     * @param string                 $sourceContainer      name of container
     * @param string                 $sourceBlob           name of blob
     * @param Models\CopyBlobOptions $options              optional parameters
     * 
     * @return none
     * 
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/dd894037.aspx
     */
    public function copyBlob($destinationContainer, $destinationBlob,
        $sourceContainer, $sourceBlob, $options = null
    ) {
        throw new \Exception(Resources::NOT_IMPLEMENTED_MSG);
    }
    
    /**
     * Establishes an exclusive one-minute write lock on a blob. To write to a locked
     * blob, a client must provide a lease ID.
     * 
     * @param string                     $container name of the container
     * @param string                     $blob      name of the blob
     * @param Models\AcquireLeaseOptions $options   optional parameters
     * 
     * @return Models\AcquireLeaseResult
     * 
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/ee691972.aspx
     */
    public function acquireLease($container, $blob, $options = null)
    {
        throw new \Exception(Resources::NOT_IMPLEMENTED_MSG);
    }
    
    /**
     * Renews an existing lease
     * 
     * @param string                    $container name of the container
     * @param string                    $blob      name of the blob
     * @param string                    $leaseId   lease id when acquiring
     * @param Models\BlobServiceOptions $options   optional parameters
     * 
     * @return Models\AcquireLeaseResult
     * 
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/ee691972.aspx
     */
    public function renewLease($container, $blob, $leaseId, $options = null)
    {
        throw new \Exception(Resources::NOT_IMPLEMENTED_MSG);
    }
    
    /**
     * Frees the lease if it is no longer needed so that another client may 
     * immediately acquire a lease against the blob.
     * 
     * @param string                    $container name of the container
     * @param string                    $blob      name of the blob
     * @param string                    $leaseId   lease id when acquiring
     * @param Models\BlobServiceOptions $options   optional parameters
     * 
     * @return none
     * 
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/ee691972.aspx
     */
    public function releaseLease($container, $blob, $leaseId, $options = null)
    {
        throw new \Exception(Resources::NOT_IMPLEMENTED_MSG);
    }
    
    /**
     * Ends the lease but ensure that another client cannot acquire a new lease until
     * the current lease period has expired.
     * 
     * @param string                    $container name of the container
     * @param string                    $blob      name of the blob
     * @param string                    $leaseId   lease id when acquiring
     * @param Models\BlobServiceOptions $options   optional parameters
     * 
     * @return none
     * 
     * @see http://msdn.microsoft.com/en-us/library/windowsazure/ee691972.aspx
     */
    public function breakLease($container, $blob, $leaseId, $options = null)
    {
        throw new \Exception(Resources::NOT_IMPLEMENTED_MSG);
    }
}

?>
