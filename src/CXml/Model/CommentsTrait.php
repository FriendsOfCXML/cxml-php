<?php

namespace CXml\Model;

use JMS\Serializer\Annotation as Serializer;

trait CommentsTrait
{
    /**
     * @var Comment[]
     */
    #[Serializer\XmlList(entry: 'Comments', inline: true)]
    #[Serializer\Type('array<CXml\Model\Comment>')]
    private ?array $comments = null;

    public function addCommentAsString(string $comment, string $type = null, string $lang = null): self
    {
        if (null === $this->comments) {
            $this->comments = [];
        }

        return $this->addComment(new Comment($comment, $type, $lang));
    }

    public function addComment(Comment $comment): self
    {
        if (null === $this->comments) {
            $this->comments = [];
        }

        $this->comments[] = $comment;

        return $this;
    }

    public function getComments(): ?array
    {
        return $this->comments;
    }

    public function getCommentsAsString(): ?string
    {
        $commentStrings = [];

        if ($comments = $this->getComments()) {
            foreach ($comments as $comment) {
                $commentStrings[] = $comment->getValue();
            }
        }

        return [] === $commentStrings ? null : \implode("\n", $commentStrings);
    }
}
