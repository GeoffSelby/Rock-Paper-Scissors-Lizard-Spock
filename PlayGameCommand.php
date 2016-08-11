<?php
namespace App;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PlayGameCommand extends Command
{
    const ROCK     = 0;
    const PAPER    = 1;
    const SCISSORS = 2;
    const LIZARD   = 3;
    const SPOCK    = 4;

    /**
     * Moves used in validation.
     *
     * @var array
     */
    private $moves = [
        self::ROCK     => 'rock',
        self::PAPER    => 'paper',
        self::SCISSORS => 'scissors',
        self::LIZARD   => 'lizard',
        self::SPOCK    => 'spock'
    ];

    /**
     * Win scenarios.
     *
     * @var array
     */
    private $winners = [
        self::ROCK     => [
            self::SCISSORS => 'Rock crushes scissors',
            self::LIZARD   => 'Rock crushes lizard'
        ],
        self::PAPER    => [
            self::ROCK  => 'Paper covers rock',
            self::SPOCK => 'Paper disproves Spock'
        ],
        self::SCISSORS => [
            self::PAPER  => 'Scissors cut paper',
            self::LIZARD => 'Scissors decapitate lizard'
        ],
        self::LIZARD   => [
            self::SPOCK => 'Lizard poisons Spock',
            self::PAPER => 'Lizard eats paper'
        ],
        self::SPOCK    => [
            self::SCISSORS => 'Spock smashes scissors',
            self::ROCK     => 'Spock vaporizes rock'
        ]
    ];

    /**
     * Command configuration.
     */
    protected function configure()
    {
        $this->setName('play')
            ->setDescription('Let\'s play Rock-Paper-Scissors-Lizard-Spock');
    }

    /**
     * Command execution.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Let\'s play Rock-Paper-Scissors-Lizard-Spock</info>');
        $output->writeln('');
        $output->writeln('<comment>Choose your move: Rock, Paper, Scissors, Lizard, Spock</comment>');

        // Let's gather the input and validate
        $helper = $this->getHelper('question');
        $question = new Question('What do you choose?: ');
        $question->setMaxAttempts(5);
        $question->setValidator(function($answer) use ($output) {
            $userMove = array_search(strtolower(trim($answer)), $this->moves);
            if ($userMove === false) {
                throw new \RuntimeException('That\'s not a valid move! Try again.');
            }
            return $userMove;
        });

        // Get a move as an integer.
        try {
            $move = $helper->ask($input, $output, $question);
        } catch (\RuntimeException $e) {
            $output->writeln('<error>Read the rules.</error>');
            return;
        }
        $randomMove = $this->randomMove($move);

        $output->writeln('Computer played: ' . $this->moveToString($randomMove));
        $output->writeln('Winner: ' . $this->determineWinner($move, $randomMove));
    }

    /**
     * Converts move integer to string.
     *
     * @param int $move
     * @return string
     */
    protected function moveToString($move)
    {
        return ucfirst($this->moves[$move]);
    }

    /**
     * Pick a random move.
     *
     * @param $move
     * @return int
     */
    protected function randomMove($move)
    {
        return array_rand($this->moves);
    }

    /**
     * Determine who the winner is.
     *
     * @param $playerMove
     * @param $randomMove
     * @return string
     */
    protected function determineWinner($playerMove, $randomMove)
    {
        $userWon = isset($this->winners[$playerMove][$randomMove]);
        if ($userWon) return $this->winners[$playerMove][$randomMove] . ', <info>you win</info>!';

        $computerWon = isset($this->winners[$randomMove][$playerMove]);
        if ($computerWon) return $this->winners[$randomMove][$playerMove] . ', <error>you lost</error>!';

        return 'Looks like a tie!';
    }
}