<?php
require dirname(__DIR__) . '/WindowsAzure/WindowsAzure.php';

spl_autoload_register(
   function($class) {
      static $classes = null;
      if ($classes === null) {
         $classes = array(
            'invalidargumenttypeexceptiontest' => '/unit/WindowsAzure/Common/Internal/InvalidArgumentTypeExceptionTest.php',
            'serviceexceptiontest' => '/unit/WindowsAzure/Common/Internal/ServiceExceptionTest.php',
            'sharedkeyfiltertest' => '/unit/WindowsAzure/Common/Internal/Filters/SharedKeyFilterTest.php',
            'tests\\framework\\blobservicerestproxytestbase' => '/framework/BlobServiceRestProxyTestBase.php',
            'tests\\framework\\fiddlerfilter' => '/framework/FiddlerFilter.php',
            'tests\\framework\\queueservicerestproxytestbase' => '/framework/QueueServiceRestProxyTestBase.php',
            'tests\\framework\\restproxytestbase' => '/framework/RestProxyTestBase.php',
            'tests\\framework\\servicebusrestproxytestbase' => '/framework/ServiceBusRestProxyTestBase.php',
            'tests\\framework\\servicemanagementrestproxytestbase' => '/framework/ServiceManagementRestProxyTestBase.php',
            'tests\\framework\\servicerestproxytestbase' => '/framework/ServiceRestProxyTestBase.php',
            'tests\\framework\\tableservicerestproxytestbase' => '/framework/TableServiceRestProxyTestBase.php',
            'tests\\framework\\testresources' => '/framework/TestResources.php',
            'tests\\framework\\virtualfilesystem' => '/framework/VirtualFileSystem.php',
            'tests\\framework\\wraprestproxytestbase' => '/framework/WrapRestProxyTestBase.php',
            'tests\\functional\\windowsazure\\blob\\blobserviceintegrationtest' => '/functional/WindowsAzure/Blob/BlobServiceIntegrationTest.php',
            'tests\\functional\\windowsazure\\blob\\integrationtestbase' => '/functional/WindowsAzure/Blob/IntegrationTestBase.php',
            'tests\\functional\\windowsazure\\queue\\functionaltestbase' => '/functional/WindowsAzure/Queue/FunctionalTestBase.php',
            'tests\\functional\\windowsazure\\queue\\integrationtestbase' => '/functional/WindowsAzure/Queue/IntegrationTestBase.php',
            'tests\\functional\\windowsazure\\queue\\queueservicefunctionaloptionstest' => '/functional/WindowsAzure/Queue/QueueServiceFunctionalOptionsTest.php',
            'tests\\functional\\windowsazure\\queue\\queueservicefunctionalparametertest' => '/functional/WindowsAzure/Queue/QueueServiceFunctionalParameterTest.php',
            'tests\\functional\\windowsazure\\queue\\queueservicefunctionaltest' => '/functional/WindowsAzure/Queue/QueueServiceFunctionalTest.php',
            'tests\\functional\\windowsazure\\queue\\queueservicefunctionaltestdata' => '/functional/WindowsAzure/Queue/QueueServiceFunctionalTestData.php',
            'tests\\functional\\windowsazure\\queue\\queueserviceintegrationtest' => '/functional/WindowsAzure/Queue/QueueServiceIntegrationTest.php',
            'tests\\functional\\windowsazure\\servicebus\\wraprestproxyintegrationtest' => '/functional/WindowsAzure/ServiceBus/WrapRestProxyIntegrationTest.php',
            'tests\\functional\\windowsazure\\servicebus\\wraptokenmanagerintegrationtest' => '/functional/WindowsAzure/ServiceBus/WrapTokenManagerIntegrationTest.php',
            'tests\\functional\\windowsazure\\servicebus\\wraptokenmanagertest' => '/functional/WindowsAzure/ServiceBus/WrapTokenManagerTest.php',
            'tests\\functional\\windowsazure\\table\\batchworkerconfig' => '/functional/WindowsAzure/Table/TableServiceFunctionalTest.php',
            'tests\\functional\\windowsazure\\table\\concurtype' => '/functional/WindowsAzure/Table/TableServiceFunctionalTest.php',
            'tests\\functional\\windowsazure\\table\\faketableinfoentry' => '/functional/WindowsAzure/Table/TableServiceFunctionalTestData.php',
            'tests\\functional\\windowsazure\\table\\faketentityentry' => '/functional/WindowsAzure/Table/TableServiceFunctionalTestData.php',
            'tests\\functional\\windowsazure\\table\\functionaltestbase' => '/functional/WindowsAzure/Table/FunctionalTestBase.php',
            'tests\\functional\\windowsazure\\table\\integrationtestbase' => '/functional/WindowsAzure/Table/IntegrationTestBase.php',
            'tests\\functional\\windowsazure\\table\\mutatepivot' => '/functional/WindowsAzure/Table/TableServiceFunctionalTestUtils.php',
            'tests\\functional\\windowsazure\\table\\optype' => '/functional/WindowsAzure/Table/TableServiceFunctionalTest.php',
            'tests\\functional\\windowsazure\\table\\tableservicefunctionaloptionstest' => '/functional/WindowsAzure/Table/TableServiceFunctionalOptionsTest.php',
            'tests\\functional\\windowsazure\\table\\tableservicefunctionalparameterstest' => '/functional/WindowsAzure/Table/TableServiceFunctionalParametersTest.php',
            'tests\\functional\\windowsazure\\table\\tableservicefunctionalquerytest' => '/functional/WindowsAzure/Table/TableServiceFunctionalQueryTest.php',
            'tests\\functional\\windowsazure\\table\\tableservicefunctionaltest' => '/functional/WindowsAzure/Table/TableServiceFunctionalTest.php',
            'tests\\functional\\windowsazure\\table\\tableservicefunctionaltestdata' => '/functional/WindowsAzure/Table/TableServiceFunctionalTestData.php',
            'tests\\functional\\windowsazure\\table\\tableservicefunctionaltestutils' => '/functional/WindowsAzure/Table/TableServiceFunctionalTestUtils.php',
            'tests\\functional\\windowsazure\\table\\tableserviceintegrationtest' => '/functional/WindowsAzure/Table/TableServiceIntegrationTest.php',
            'tests\\mock\\windowsazure\\common\\internal\\authentication\\sharedkeyauthschememock' => '/mock/WindowsAzure/Common/Internal/Authentication/SharedKeyAuthSchemeMock.php',
            'tests\\mock\\windowsazure\\common\\internal\\authentication\\storageauthschememock' => '/mock/WindowsAzure/Common/Internal/Authentication/StorageAuthSchemeMock.php',
            'tests\\mock\\windowsazure\\common\\internal\\authentication\\tablesharedkeyliteauthschememock' => '/mock/WindowsAzure/Common/Internal/Authentication/TableSharedKeyLiteAuthSchemeMock.php',
            'tests\\mock\\windowsazure\\common\\internal\\filters\\simplefiltermock' => '/mock/WindowsAzure/Common/Internal/Filters/SimpleFilterMock.php',
            'tests\\unit\\windowsazure\\blob\\blobrestproxytest' => '/unit/WindowsAzure/Blob/Internal/BlobRestProxyTest.php',
            'tests\\unit\\windowsazure\\blob\\blobservicetest' => '/unit/WindowsAzure/Blob/BlobServiceTest.php',
            'tests\\unit\\windowsazure\\blob\\models\\accessconditiontest' => '/unit/WindowsAzure/Blob/Models/AccessConditionTest.php',
            'tests\\unit\\windowsazure\\blob\\models\\accesspolicytest' => '/unit/WindowsAzure/Blob/Models/AccessPolicyTest.php',
            'tests\\unit\\windowsazure\\blob\\models\\acquireleaseoptionstest' => '/unit/WindowsAzure/Blob/Models/AcquireLeaseOptionsTest.php',
            'tests\\unit\\windowsazure\\blob\\models\\acquireleaseresulttest' => '/unit/WindowsAzure/Blob/Models/AcquireLeaseResultTest.php',
            'tests\\unit\\windowsazure\\blob\\models\\blobblocktypetest' => '/unit/WindowsAzure/Blob/Models/BlobBlockTypeTest.php',
            'tests\\unit\\windowsazure\\blob\\models\\blobprefixtest' => '/unit/WindowsAzure/Blob/Models/BlobPrefixTest.php',
            'tests\\unit\\windowsazure\\blob\\models\\blobpropertiestest' => '/unit/WindowsAzure/Blob/Models/BlobPropertiesTest.php',
            'tests\\unit\\windowsazure\\blob\\models\\blobserviceoptionstest' => '/unit/WindowsAzure/Blob/Models/BlobServiceOptionsTest.php',
            'tests\\unit\\windowsazure\\blob\\models\\blobtest' => '/unit/WindowsAzure/Blob/Models/BlobTest.php',
            'tests\\unit\\windowsazure\\blob\\models\\blobtypetest' => '/unit/WindowsAzure/Blob/Models/BlobTypeTest.php',
            'tests\\unit\\windowsazure\\blob\\models\\blocklisttest' => '/unit/WindowsAzure/Blob/Models/BlockListTest.php',
            'tests\\unit\\windowsazure\\blob\\models\\blocktest' => '/unit/WindowsAzure/Blob/Models/BlockTest.php',
            'tests\\unit\\windowsazure\\blob\\models\\commitblobblocksoptionstest' => '/unit/WindowsAzure/Blob/Models/CommitBlobBlocksOptionsTest.php',
            'tests\\unit\\windowsazure\\blob\\models\\containeracltest' => '/unit/WindowsAzure/Blob/Models/ContainerACLTest.php',
            'tests\\unit\\windowsazure\\blob\\models\\containerpropertiestest' => '/unit/WindowsAzure/Blob/Models/ContainerPropertiesTest.php',
            'tests\\unit\\windowsazure\\blob\\models\\containertest' => '/unit/WindowsAzure/Blob/Models/ContainerTest.php',
            'tests\\unit\\windowsazure\\blob\\models\\copybloboptionstest' => '/unit/WindowsAzure/Blob/Models/CopyBlobOptionsTest.php',
            'tests\\unit\\windowsazure\\blob\\models\\createblobblockoptionstest' => '/unit/WindowsAzure/Blob/Models/CreateBlobBlockOptionsTest.php',
            'tests\\unit\\windowsazure\\blob\\models\\createbloboptionstest' => '/unit/WindowsAzure/Blob/Models/CreateBlobOptionsTest.php',
            'tests\\unit\\windowsazure\\blob\\models\\createblobpagesoptionstest' => '/unit/WindowsAzure/Blob/Models/CreateBlobPagesOptionsTest.php',
            'tests\\unit\\windowsazure\\blob\\models\\createblobpagesresulttest' => '/unit/WindowsAzure/Blob/Models/CreateBlobPagesResultTest.php',
            'tests\\unit\\windowsazure\\blob\\models\\createblobsnapshotoptionstest' => '/unit/WindowsAzure/Blob/Models/CreateBlobSnapshotOptionsTest.php',
            'tests\\unit\\windowsazure\\blob\\models\\createblobsnapshotresulttest' => '/unit/WindowsAzure/Blob/Models/CreateBlobSnapshotResultTest.php',
            'tests\\unit\\windowsazure\\blob\\models\\createcontaineroptionstest' => '/unit/WindowsAzure/Blob/Models/CreateContainerOptionsTest.php',
            'tests\\unit\\windowsazure\\blob\\models\\deletebloboptionstest' => '/unit/WindowsAzure/Blob/Models/DeleteBlobOptionsTest.php',
            'tests\\unit\\windowsazure\\blob\\models\\deletecontaineroptionstest' => '/unit/WindowsAzure/Blob/Models/DeleteContainerOptionsTest.php',
            'tests\\unit\\windowsazure\\blob\\models\\getblobmetadataoptionstest' => '/unit/WindowsAzure/Blob/Models/GetBlobMetadataOptionsTest.php',
            'tests\\unit\\windowsazure\\blob\\models\\getblobmetadataresulttest' => '/unit/WindowsAzure/Blob/Models/GetBlobMetadataResultTest.php',
            'tests\\unit\\windowsazure\\blob\\models\\getbloboptionstest' => '/unit/WindowsAzure/Blob/Models/GetBlobOptionsTest.php',
            'tests\\unit\\windowsazure\\blob\\models\\getblobpropertiesoptionstest' => '/unit/WindowsAzure/Blob/Models/GetBlobPropertiesOptionsTest.php',
            'tests\\unit\\windowsazure\\blob\\models\\getblobpropertiesresulttest' => '/unit/WindowsAzure/Blob/Models/GetBlobPropertiesResultTest.php',
            'tests\\unit\\windowsazure\\blob\\models\\getblobresulttest' => '/unit/WindowsAzure/Blob/Models/GetBlobResultTest.php',
            'tests\\unit\\windowsazure\\blob\\models\\getcontaineraclresulttest' => '/unit/WindowsAzure/Blob/Models/GetContainerACLResultTest.php',
            'tests\\unit\\windowsazure\\blob\\models\\getcontainerpropertiesresulttest' => '/unit/WindowsAzure/Blob/Models/GetContainerPropertiesResultTest.php',
            'tests\\unit\\windowsazure\\blob\\models\\leasemodetest' => '/unit/WindowsAzure/Blob/Models/LeaseModeTest.php',
            'tests\\unit\\windowsazure\\blob\\models\\listblobblocksoptionstest' => '/unit/WindowsAzure/Blob/Models/ListBlobBlocksOptionsTest.php',
            'tests\\unit\\windowsazure\\blob\\models\\listblobblocksresulttest' => '/unit/WindowsAzure/Blob/Models/ListBlobBlocksResultTest.php',
            'tests\\unit\\windowsazure\\blob\\models\\listblobsoptionstest' => '/unit/WindowsAzure/Blob/Models/ListBlobsOptionsTest.php',
            'tests\\unit\\windowsazure\\blob\\models\\listblobsresulttest' => '/unit/WindowsAzure/Blob/Models/ListBlobsResultTest.php',
            'tests\\unit\\windowsazure\\blob\\models\\listcontainersoptionstest' => '/unit/WindowsAzure/Blob/Models/ListContainersOptionsTest.php',
            'tests\\unit\\windowsazure\\blob\\models\\listcontainersresulttest' => '/unit/WindowsAzure/Blob/Models/ListContainersResultTest.php',
            'tests\\unit\\windowsazure\\blob\\models\\listpageblobrangesoptionstest' => '/unit/WindowsAzure/Blob/Models/ListPageBlobRangesOptionsTest.php',
            'tests\\unit\\windowsazure\\blob\\models\\listpageblobrangesresulttest' => '/unit/WindowsAzure/Blob/Models/ListPageBlobRangesResultTest.php',
            'tests\\unit\\windowsazure\\blob\\models\\pagerangetest' => '/unit/WindowsAzure/Blob/Models/PageRangeTest.php',
            'tests\\unit\\windowsazure\\blob\\models\\publicaccesstypetest' => '/unit/WindowsAzure/Blob/Models/PublicAccessTypeTest.php',
            'tests\\unit\\windowsazure\\blob\\models\\setblobmetadataoptionstest' => '/unit/WindowsAzure/Blob/Models/SetBlobMetadataOptionsTest.php',
            'tests\\unit\\windowsazure\\blob\\models\\setblobmetadataresulttest' => '/unit/WindowsAzure/Blob/Models/SetBlobMetadataResultTest.php',
            'tests\\unit\\windowsazure\\blob\\models\\setblobpropertiesoptionstest' => '/unit/WindowsAzure/Blob/Models/SetBlobPropertiesOptionsTest.php',
            'tests\\unit\\windowsazure\\blob\\models\\setblobpropertiesresulttest' => '/unit/WindowsAzure/Blob/Models/SetBlobPropertiesResultTest.php',
            'tests\\unit\\windowsazure\\blob\\models\\setcontainermetadataoptionstest' => '/unit/WindowsAzure/Blob/Models/SetContainerMetadataOptionsTest.php',
            'tests\\unit\\windowsazure\\blob\\models\\signedidentifiertest' => '/unit/WindowsAzure/Blob/Models/SignedIdentifierTest.php',
            'tests\\unit\\windowsazure\\common\\internal\\authentication\\sharedkeyauthschemetest' => '/unit/WindowsAzure/Common/Internal/Authentication/SharedKeyAuthSchemeTest.php',
            'tests\\unit\\windowsazure\\common\\internal\\authentication\\storageauthschemetest' => '/unit/WindowsAzure/Common/Internal/Authentication/StorageAuthSchemeTest.php',
            'tests\\unit\\windowsazure\\common\\internal\\authentication\\tablesharedkeyliteauthschemetest' => '/unit/WindowsAzure/Common/Internal/Authentication/TableSharedKeyLiteAuthSchemeTest.php',
            'tests\\unit\\windowsazure\\common\\internal\\configurationtest' => '/unit/WindowsAzure/Common/ConfigurationTest.php',
            'tests\\unit\\windowsazure\\common\\internal\\filters\\datefiltertest' => '/unit/WindowsAzure/Common/Internal/Filters/DateFilterTest.php',
            'tests\\unit\\windowsazure\\common\\internal\\filters\\exponentialretrypolicytest' => '/unit/WindowsAzure/Common/Internal/Filters/ExponentialRetryPolicyTest.php',
            'tests\\unit\\windowsazure\\common\\internal\\filters\\headersfiltertest' => '/unit/WindowsAzure/Common/Internal/Filters/HeadersFilterTest.php',
            'tests\\unit\\windowsazure\\common\\internal\\http\\httpcallcontexttest' => '/unit/WindowsAzure/Common/Internal/Http/HttpCallContextTest.php',
            'tests\\unit\\windowsazure\\common\\internal\\http\\httpclienttest' => '/unit/WindowsAzure/Common/Internal/Http/HttpClientTest.php',
            'tests\\unit\\windowsazure\\common\\internal\\http\\urltest' => '/unit/WindowsAzure/Common/Internal/Http/UrlTest.php',
            'tests\\unit\\windowsazure\\common\\internal\\serialization\\xmlserializertest' => '/unit/WindowsAzure/Common/Internal/Serialization/XmlSerializerTest.php',
            'tests\\unit\\windowsazure\\common\\internal\\servicerestproxytest' => '/unit/WindowsAzure/Common/Internal/ServiceRestProxyTest.php',
            'tests\\unit\\windowsazure\\common\\internal\\servicesbuildertest' => '/unit/WindowsAzure/Common/Internal/ServicesBuilderTest.php',
            'tests\\unit\\windowsazure\\common\\models\\getservicepropertiesresulttest' => '/unit/WindowsAzure/Common/Models/GetServicePropertiesResultTest.php',
            'tests\\unit\\windowsazure\\common\\models\\loggingtest' => '/unit/WindowsAzure/Common/Models/LoggingTest.php',
            'tests\\unit\\windowsazure\\common\\models\\metricstest' => '/unit/WindowsAzure/Common/Models/MetricsTest.php',
            'tests\\unit\\windowsazure\\common\\models\\retentionpolicytest' => '/unit/WindowsAzure/Common/Models/RetentionPolicyTest.php',
            'tests\\unit\\windowsazure\\common\\models\\servicepropertiestest' => '/unit/WindowsAzure/Common/Models/ServicePropertiesTest.php',
            'tests\\unit\\windowsazure\\createstorageserviceoptionsmanagement\\models\\createstorageserviceoptionstest' => '/unit/WindowsAzure/ServiceManagement/Models/CreateStorageServiceOptionsTest.php',
            'tests\\unit\\windowsazure\\loggertest' => '/unit/WindowsAzure/Common/Internal/LoggerTest.php',
            'tests\\unit\\windowsazure\\queue\\internal\\queuerestproxytest' => '/unit/WindowsAzure/Queue/Internal/QueueRestProxyTest.php',
            'tests\\unit\\windowsazure\\queue\\models\\createmessageoptionstest' => '/unit/WindowsAzure/Queue/Models/CreateMessageOptionsTest.php',
            'tests\\unit\\windowsazure\\queue\\models\\createqueueoptionstest' => '/unit/WindowsAzure/Queue/Models/CreateQueueOptionsTest.php',
            'tests\\unit\\windowsazure\\queue\\models\\getqueuemetadataresulttest' => '/unit/WindowsAzure/Queue/Models/GetQueueMetadataResultTest.php',
            'tests\\unit\\windowsazure\\queue\\models\\listmessagesoptionstest' => '/unit/WindowsAzure/Queue/Models/ListMessagesOptionsTest.php',
            'tests\\unit\\windowsazure\\queue\\models\\listmessagesresulttest' => '/unit/WindowsAzure/Queue/Models/ListMessagesResultTest.php',
            'tests\\unit\\windowsazure\\queue\\models\\listqueuesoptionstest' => '/unit/WindowsAzure/Queue/Models/ListQueuesOptionsTest.php',
            'tests\\unit\\windowsazure\\queue\\models\\listqueuesresulttest' => '/unit/WindowsAzure/Queue/Models/ListQueuesResultTest.php',
            'tests\\unit\\windowsazure\\queue\\models\\peekmessagesoptionstest' => '/unit/WindowsAzure/Queue/Models/PeekMessagesOptionsTest.php',
            'tests\\unit\\windowsazure\\queue\\models\\peekmessagesresulttest' => '/unit/WindowsAzure/Queue/Models/PeekMessagesResultTest.php',
            'tests\\unit\\windowsazure\\queue\\models\\queuemessagetest' => '/unit/WindowsAzure/Queue/Models/QueueMessageTest.php',
            'tests\\unit\\windowsazure\\queue\\models\\queueserviceoptionstest' => '/unit/WindowsAzure/Queue/Models/QueueServiceOptionsTest.php',
            'tests\\unit\\windowsazure\\queue\\models\\queuetest' => '/unit/WindowsAzure/Queue/Models/QueueTest.php',
            'tests\\unit\\windowsazure\\queue\\models\\updatemessageresulttest' => '/unit/WindowsAzure/Queue/Models/UpdateMessageResultTest.php',
            'tests\\unit\\windowsazure\\queue\\models\\windowsazurequeuemessagetest' => '/unit/WindowsAzure/Queue/Models/WindowsAzureQueueMessageTest.php',
            'tests\\unit\\windowsazure\\queue\\queueservicetest' => '/unit/WindowsAzure/Queue/QueueServiceTest.php',
            'tests\\unit\\windowsazure\\servicebus\\internal\\servicebusrestproxytest' => '/unit/WindowsAzure/ServiceBus/Internal/ServiceBusRestProxyTest.php',
            'tests\\unit\\windowsazure\\servicebus\\internal\\wraprestproxytest' => '/unit/WindowsAzure/ServiceBus/Internal/WrapRestProxyTest.php',
            'tests\\unit\\windowsazure\\servicebus\\internal\\wraptokenmanagertest' => '/unit/WindowsAzure/ServiceBus/Internal/WrapTokenManagerTest.php',
            'tests\\unit\\windowsazure\\servicebus\\models\\activetokentest' => '/unit/WindowsAzure/ServiceBus/models/ActiveTokenTest.php',
            'tests\\unit\\windowsazure\\servicebus\\models\\wrapaccesstokenresulttest' => '/unit/WindowsAzure/ServiceBus/models/WrapAccessTokenResultTest.php',
            'tests\\unit\\windowsazure\\servicebus\\servicebusservicetest' => '/unit/WindowsAzure/ServiceBus/ServiceBusServiceTest.php',
            'tests\\unit\\windowsazure\\servicebus\\servicebussettingstest' => '/unit/WindowsAzure/ServiceBus/ServiceBusSettingsTest.php',
            'tests\\unit\\windowsazure\\servicebus\\wrapservicetest' => '/unit/WindowsAzure/ServiceBus/WrapServiceTest.php',
            'tests\\unit\\windowsazure\\servicemanagement\\internal\\servicemanagementrestproxytest' => '/unit/WindowsAzure/ServiceManagement/Internal/ServiceManagementRestProxyTest.php',
            'tests\\unit\\windowsazure\\servicemanagement\\models\\affinitygrouptest' => '/unit/WindowsAzure/ServiceManagement/Models/AffinityGroupTest.php',
            'tests\\unit\\windowsazure\\servicemanagement\\models\\asynchronousoperationresulttest' => '/unit/WindowsAzure/ServiceManagement/Models/AsynchronousOperationResultTest.php',
            'tests\\unit\\windowsazure\\servicemanagement\\models\\createaffinitygroupoptionstest' => '/unit/WindowsAzure/ServiceManagement/Models/CreateAffinityGroupOptionsTest.php',
            'tests\\unit\\windowsazure\\servicemanagement\\models\\getaffinitygrouppropertiesresulttest' => '/unit/WindowsAzure/ServiceManagement/Models/GetAffinityGroupPropertiesResultTest.php',
            'tests\\unit\\windowsazure\\servicemanagement\\models\\getoperationstatusresulttest' => '/unit/WindowsAzure/ServiceManagement/Models/GetOperationStatusResultTest.php',
            'tests\\unit\\windowsazure\\servicemanagement\\models\\getstorageservicekeysresulttest' => '/unit/WindowsAzure/ServiceManagement/Models/GetStorageServiceKeysResultTest.php',
            'tests\\unit\\windowsazure\\servicemanagement\\models\\getstorageservicepropertiesresulttest' => '/unit/WindowsAzure/ServiceManagement/Models/GetStorageServicePropertiesResultTest.php',
            'tests\\unit\\windowsazure\\servicemanagement\\models\\listaffinitygroupsresulttest' => '/unit/WindowsAzure/ServiceManagement/Models/ListAffinityGroupsResultTest.php',
            'tests\\unit\\windowsazure\\servicemanagement\\models\\listlocationsresulttest' => '/unit/WindowsAzure/ServiceManagement/Models/ListLocationsResultTest.php',
            'tests\\unit\\windowsazure\\servicemanagement\\models\\liststorageservicesresulttest' => '/unit/WindowsAzure/ServiceManagement/Models/ListStorageServicesResultTest.php',
            'tests\\unit\\windowsazure\\servicemanagement\\models\\locationtest' => '/unit/WindowsAzure/ServiceManagement/Models/LocationTest.php',
            'tests\\unit\\windowsazure\\servicemanagement\\models\\servicepropertiestest' => '/unit/WindowsAzure/ServiceManagement/Models/ServicePropertiesTest.php',
            'tests\\unit\\windowsazure\\servicemanagement\\models\\servicetest' => '/unit/WindowsAzure/ServiceManagement/Models/ServiceTest.php',
            'tests\\unit\\windowsazure\\servicemanagement\\models\\storageservicetest' => '/unit/WindowsAzure/ServiceManagement/Models/StorageServiceTest.php',
            'tests\\unit\\windowsazure\\servicemanagement\\models\\updatestorageserviceoptionstest' => '/unit/WindowsAzure/ServiceManagement/Models/UpdateStorageServiceOptionsTest.php',
            'tests\\unit\\windowsazure\\servicemanagement\\servicemanagementservicetest' => '/unit/WindowsAzure/ServiceManagement/ServiceManagementServiceTest.php',
            'tests\\unit\\windowsazure\\serviceruntime\\internal\\acquirecurrentstatetest' => '/unit/WindowsAzure/ServiceRuntime/Internal/AcquireCurrentStateTest.php',
            'tests\\unit\\windowsazure\\serviceruntime\\internal\\chunkedgoalstatedeserializertest' => '/unit/WindowsAzure/ServiceRuntime/Internal/ChunkedGoalStateDeserializerTest.php',
            'tests\\unit\\windowsazure\\serviceruntime\\internal\\currentstatetest' => '/unit/WindowsAzure/ServiceRuntime/Internal/CurrentStateTest.php',
            'tests\\unit\\windowsazure\\serviceruntime\\internal\\fileinputchanneltest' => '/unit/WindowsAzure/ServiceRuntime/Internal/FileInputChannelTest.php',
            'tests\\unit\\windowsazure\\serviceruntime\\internal\\fileoutputchanneltest' => '/unit/WindowsAzure/ServiceRuntime/Internal/FileOutputChannelTest.php',
            'tests\\unit\\windowsazure\\serviceruntime\\internal\\goalstatetest' => '/unit/WindowsAzure/ServiceRuntime/Internal/GoalStateTest.php',
            'tests\\unit\\windowsazure\\serviceruntime\\internal\\localresourcetest' => '/unit/WindowsAzure/ServiceRuntime/Internal/LocalResourceTest.php',
            'tests\\unit\\windowsazure\\serviceruntime\\internal\\protocol1runtimeclientfactorytest' => '/unit/WindowsAzure/ServiceRuntime/Internal/Protocol1RuntimeClientFactoryTest.php',
            'tests\\unit\\windowsazure\\serviceruntime\\internal\\protocol1runtimeclienttest' => '/unit/WindowsAzure/ServiceRuntime/Internal/Protocol1RuntimeClientTest.php',
            'tests\\unit\\windowsazure\\serviceruntime\\internal\\protocol1runtimecurrentstateclienttest' => '/unit/WindowsAzure/ServiceRuntime/Internal/Protocol1RuntimeCurrentStateClientTest.php',
            'tests\\unit\\windowsazure\\serviceruntime\\internal\\protocol1runtimegoalstateclienttest' => '/unit/WindowsAzure/ServiceRuntime/Internal/Protocol1RuntimeGoalStateClientTest.php',
            'tests\\unit\\windowsazure\\serviceruntime\\internal\\releasecurrentstatetest' => '/unit/WindowsAzure/ServiceRuntime/Internal/ReleaseCurrentStateTest.php',
            'tests\\unit\\windowsazure\\serviceruntime\\internal\\roleenvironmentconfigurationsettingchangetest' => '/unit/WindowsAzure/ServiceRuntime/Internal/RoleEnvironmentConfigurationSettingChangeTest.php',
            'tests\\unit\\windowsazure\\serviceruntime\\internal\\roleenvironmentdatatest' => '/unit/WindowsAzure/ServiceRuntime/Internal/RoleEnvironmentDataTest.php',
            'tests\\unit\\windowsazure\\serviceruntime\\internal\\roleenvironmenttopologychangetest' => '/unit/WindowsAzure/ServiceRuntime/Internal/RoleEnvironmentTopologyChangeTest.php',
            'tests\\unit\\windowsazure\\serviceruntime\\internal\\roleinstanceendpointtest' => '/unit/WindowsAzure/ServiceRuntime/Internal/RoleInstanceEndpointTest.php',
            'tests\\unit\\windowsazure\\serviceruntime\\internal\\roleinstancetest' => '/unit/WindowsAzure/ServiceRuntime/Internal/RoleInstanceTest.php',
            'tests\\unit\\windowsazure\\serviceruntime\\internal\\roletest' => '/unit/WindowsAzure/ServiceRuntime/Internal/RoleTest.php',
            'tests\\unit\\windowsazure\\serviceruntime\\internal\\runtimekerneltest' => '/unit/WindowsAzure/ServiceRuntime/Internal/RuntimeKernelTest.php',
            'tests\\unit\\windowsazure\\serviceruntime\\internal\\runtimeversionmanagertest' => '/unit/WindowsAzure/ServiceRuntime/Internal/RuntimeVersionManagerTest.php',
            'tests\\unit\\windowsazure\\serviceruntime\\internal\\runtimeversionprotocolclienttest' => '/unit/WindowsAzure/ServiceRuntime/Internal/RuntimeVersionProtocolClientTest.php',
            'tests\\unit\\windowsazure\\serviceruntime\\internal\\xmlcurrentstateserializertest' => '/unit/WindowsAzure/ServiceRuntime/Internal/XmlCurrentStateSerializerTest.php',
            'tests\\unit\\windowsazure\\serviceruntime\\internal\\xmlgoalstatedeserializertest' => '/unit/WindowsAzure/ServiceRuntime/Internal/XmlGoalStateDeserializerTest.php',
            'tests\\unit\\windowsazure\\serviceruntime\\internal\\xmlroleenvironmentdatadeserializertest' => '/unit/WindowsAzure/ServiceRuntime/Internal/XmlRoleEnvironmentDataDeserializerTest.php',
            'tests\\unit\\windowsazure\\serviceruntime\\myclass' => '/unit/WindowsAzure/ServiceRuntime/RoleEnvironmentTest.php',
            'tests\\unit\\windowsazure\\serviceruntime\\roleenvironmenttest' => '/unit/WindowsAzure/ServiceRuntime/RoleEnvironmentTest.php',
            'tests\\unit\\windowsazure\\table\\models\\batcherrortest' => '/unit/WindowsAzure/Table/Models/BatchErrorTest.php',
            'tests\\unit\\windowsazure\\table\\models\\batchoperationparameternametest' => '/unit/WindowsAzure/Table/Models/BatchOperationParameterNameTest.php',
            'tests\\unit\\windowsazure\\table\\models\\batchoperationstest' => '/unit/WindowsAzure/Table/Models/BatchOperationsTest.php',
            'tests\\unit\\windowsazure\\table\\models\\batchoperationtest' => '/unit/WindowsAzure/Table/Models/BatchOperationTest.php',
            'tests\\unit\\windowsazure\\table\\models\\batchoperationtypetest' => '/unit/WindowsAzure/Table/Models/BatchOperationTypeTest.php',
            'tests\\unit\\windowsazure\\table\\models\\batchresulttest' => '/unit/WindowsAzure/Table/Models/BatchResultTest.php',
            'tests\\unit\\windowsazure\\table\\models\\deleteentityoptionstest' => '/unit/WindowsAzure/Table/Models/DeleteEntityOptionsTest.php',
            'tests\\unit\\windowsazure\\table\\models\\edmtypetest' => '/unit/WindowsAzure/Table/Models/EdmTypeTest.php',
            'tests\\unit\\windowsazure\\table\\models\\entitytest' => '/unit/WindowsAzure/Table/Models/EntityTest.php',
            'tests\\unit\\windowsazure\\table\\models\\filters\\binaryfiltertest' => '/unit/WindowsAzure/Table/Models/Filters/BinaryFilterTest.php',
            'tests\\unit\\windowsazure\\table\\models\\filters\\constantfiltertest' => '/unit/WindowsAzure/Table/Models/Filters/ConstantFilterTest.php',
            'tests\\unit\\windowsazure\\table\\models\\filters\\filtertest' => '/unit/WindowsAzure/Table/Models/Filters/FilterTest.php',
            'tests\\unit\\windowsazure\\table\\models\\filters\\propertynamefiltertest' => '/unit/WindowsAzure/Table/Models/Filters/PropertyNameFilterTest.php',
            'tests\\unit\\windowsazure\\table\\models\\filters\\querystringfiltertest' => '/unit/WindowsAzure/Table/Models/Filters/QueryStringFilterTest.php',
            'tests\\unit\\windowsazure\\table\\models\\filters\\unaryfiltertest' => '/unit/WindowsAzure/Table/Models/Filters/UnaryFilterTest.php',
            'tests\\unit\\windowsazure\\table\\models\\getentityresulttest' => '/unit/WindowsAzure/Table/Models/GetEntityResultTest.php',
            'tests\\unit\\windowsazure\\table\\models\\gettableresulttest' => '/unit/WindowsAzure/Table/Models/GetTableResultTest.php',
            'tests\\unit\\windowsazure\\table\\models\\insertentityresulttest' => '/unit/WindowsAzure/Table/Models/InsertEntityResultTest.php',
            'tests\\unit\\windowsazure\\table\\models\\propertytest' => '/unit/WindowsAzure/Table/Models/PropertyTest.php',
            'tests\\unit\\windowsazure\\table\\models\\queryentitiesoptionstest' => '/unit/WindowsAzure/Table/Models/QueryEntitiesOptionsTest.php',
            'tests\\unit\\windowsazure\\table\\models\\queryentitiesresulttest' => '/unit/WindowsAzure/Table/Models/QueryEntitiesResultTest.php',
            'tests\\unit\\windowsazure\\table\\models\\querytablesoptionstest' => '/unit/WindowsAzure/Table/Models/QueryTablesOptionsTest.php',
            'tests\\unit\\windowsazure\\table\\models\\querytablesresulttest' => '/unit/WindowsAzure/Table/Models/QueryTablesResultTest.php',
            'tests\\unit\\windowsazure\\table\\models\\querytest' => '/unit/WindowsAzure/Table/Models/QueryTest.php',
            'tests\\unit\\windowsazure\\table\\models\\tableserviceoptionstest' => '/unit/WindowsAzure/Table/Models/TableServiceOptionsTest.php',
            'tests\\unit\\windowsazure\\table\\models\\updateentityresulttest' => '/unit/WindowsAzure/Table/Models/UpdateEntityResultTest.php',
            'tests\\unit\\windowsazure\\table\\tablerestproxytest' => '/unit/WindowsAzure/Table/Internal/TableRestProxyTest.php',
            'tests\\unit\\windowsazure\\table\\tableservicetest' => '/unit/WindowsAzure/Table/TableServiceTest.php',
            'tests\\unit\\windowsazure\\utilitiestest' => '/unit/WindowsAzure/Common/Internal/UtilitiesTest.php',
            'tests\\unit\\windowsazure\\validatetest' => '/unit/WindowsAzure/Common/Internal/ValidateTest.php',
            'wrapfiltertest' => '/unit/WindowsAzure/Common/Internal/Filters/WrapFilterTest.php'
          );
      }
      $cn = strtolower($class);
      if (isset($classes[$cn])) {
         require __DIR__ . $classes[$cn];
      }
   }
);
