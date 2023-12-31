<?php

namespace Ogrre\ChatGPT\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Ogrre\ChatGPT\Resources\ChatResource;

use OpenAI;

class Chat extends Model
{
    public mixed $client;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->client = OpenAI::client(config('chatgpt.secrets.api_key'));
    }

    /**
     * @var string[]
     */
    protected $fillable = [
        'id',
        'title',
        'messages'
    ];

    /**
     * @var string
     */
    protected $table = 'chats';

    /**
     * @return HasMany
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    /**
     * @return mixed
     */
    public function list_messages()
    {
        return $this->messages->map(function ($message) {
            return ['role' => $message->role, 'content' => $message->content];
        })->all();
    }

    /**
     * @param string $content
     * @param string $role
     * @return void
     */
    private function newMessage(string $content, string $role)
    {
        $messages = $this->messages();

        $message = new Message();
        $message->fill([
            "role" => $role,
            "content" => $content,
        ]);
        $messages->save($message);
    }

    /**
     * @return array
     */
    public function display()
    {
        return ["chat" => Chat::find($this->id), "messages" => Chat::find($this->id)->messages];
    }

    /**
     * @param string $prompt
     * @return array
     */
    public function gpt(string $prompt)
    {
        $this->newMessage($prompt, "user");

        $response = $this->client->chat()->create([
            'model' => config('chatgpt.openai.model'),
            'messages' => $this->list_messages(),
        ]);

        $this->newMessage($response->choices[0]->message->content, "assistant");

        self::title($prompt);

        return self::display();
    }

    /**
     * @param string $prompt
     */
    private function title(string $prompt)
    {
        if (sizeof($this->messages) < 4 and $this->title == 'New Chat') {
            $content = "Résume ce texte en quelques mots: " . $prompt;

            $response = $this->client->chat()->create([
                'model' => config('chatgpt.openai.model'),
                'messages' => [
                    ['role' => 'user', 'content' => $content],
                ],
            ]);

            $this->title = $response->choices[0]->message->content;
            $this->save();
        }
    }
}
