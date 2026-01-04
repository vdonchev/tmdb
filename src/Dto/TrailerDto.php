<?php

namespace App\Dto;

use DateMalformedStringException;
use DateTimeImmutable;

final readonly class TrailerDto
{
    /**
     * @param string[] $categories
     * @param string[] $genres
     */
    public function __construct(
        public string $id,
        public string $youtubeVideoId,
        public string $youtubeChannelId,
        public string $youtubeThumbnail,
        public string $title,
        public string $url,
        public string $thumbnail,
        public string $language,
        public array $categories,
        public array $genres,
        public DateTimeImmutable $published,
        public int $views,
    ) {
    }

    /**
     * @param array $data
     * @return self
     * @throws DateMalformedStringException
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? '',
            youtubeVideoId: $data['youtube_video_id'] ?? '',
            youtubeChannelId: $data['youtube_channel_id'] ?? '',
            youtubeThumbnail: $data['youtube_thumbnail'] ?? '',
            title: $data['title'] ?? '',
            url: $data['url'] ?? '',
            thumbnail: $data['thumbnail'] ?? '',
            language: $data['language'] ?? '',
            categories: $data['categories'] ?? [],
            genres: $data['genres'] ?? [],
            published: new DateTimeImmutable($data['published']),
            views: (int)($data['views'] ?? 0),
        );
    }
}
