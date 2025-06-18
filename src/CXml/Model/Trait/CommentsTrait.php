<?php

declare(strict_types=1);

namespace CXml\Model\Trait;

use Assert\Assertion;
use CXml\Model\Comment;
use JMS\Serializer\Annotation as Serializer;

use function implode;
use function is_array;

trait CommentsTrait
{
    /**
     * @var Comment[]
     */
    #[Serializer\XmlList(entry: 'Comments', inline: true)]
    #[Serializer\Type('array<CXml\Model\Comment>')]
    private ?array $comments = null;

    public function addCommentAsString(string $comment, ?string $type = null, ?string $lang = null): self
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

        $comments = $this->getComments();
        if (is_array($comments)) {
            Assertion::allIsInstanceOf($comments, Comment::class);
            foreach ($comments as $comment) {
                if (null === $comment->value || '' === $comment->value) {
                    continue;
                }
                $commentStrings[] = $comment->value;
            }
        }

        return [] === $commentStrings ? null : implode("\n", $commentStrings);
    }
}
