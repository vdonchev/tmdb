<?php

namespace App\Dto;

use DateTimeImmutable;
use Symfony\Component\Serializer\Attribute\SerializedName;

final readonly class TrailerDto
{
    /**
     * @param string[] $categories
     * @param string[] $genres
     */
    public function __construct(
        public string $id,

        #[SerializedName('youtube_video_id')]
        public string $youtubeVideoId,

        #[SerializedName('youtube_channel_id')]
        public string $youtubeChannelId,

        #[SerializedName('youtube_thumbnail')]
        public string $youtubeThumbnail,
        public string $title,
        public string $url,
        public string $thumbnail,
        public string $language,
        public array $categories,
        public array $genres,
        public ?DateTimeImmutable $published,
        public int $views,
    ) {
    }
}
