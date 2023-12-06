<?php

namespace CXml\Model;

use JMS\Serializer\Annotation as Ser;

trait CommentsTrait
{

	/**
	 * @Ser\XmlList(inline=true, entry="Comments")
	 * @Ser\Type("array<CXml\Model\Comment>")
	 *
	 * @var Comment[]
	 */
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

		return empty($commentStrings) ? null : \implode("\n", $commentStrings);
	}
}
