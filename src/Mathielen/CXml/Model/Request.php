<?php

namespace Mathielen\CXml\Model;

use JMS\Serializer\Annotation as Ser;

class Request
{
    /**
     * @Ser\SerializedName("Status")
     */
    private ?Status $status;

    /**
     * @Ser\XmlAttribute
     */
    private ?string $deploymentMode;

    /**
     * @Ser\XmlAttribute
     */
    private ?string $id;

    /**
     * @Ser\Exclude()
     * see JmsEventSubscriber
     */
    private ?RequestInterface $request;

    public function __construct(?RequestInterface $response, ?Status $status = null, ?string $id = null, ?string $deploymentMode = null)
    {
        $this->status = $status;
        $this->id = $id;
        $this->request = $response;
        $this->deploymentMode = $deploymentMode;
    }

    public function getStatus(): ?Status
    {
        return $this->status;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getDeploymentMode(): ?string
    {
        return $this->deploymentMode;
    }

    public function getRequest(): ?RequestInterface
    {
        return $this->request;
    }
}
