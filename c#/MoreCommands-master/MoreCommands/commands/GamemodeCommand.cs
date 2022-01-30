using MiNET;
using MiNET.Plugins;
using MiNET.Plugins.Attributes;
using MiNET.Worlds;
using System;
using System.Linq;

namespace MoreCommands.commands
{

    public class GamemodeCommand
    {

        private Loader Owner;

        public GamemodeCommand(Loader owner)
        {
            Owner = owner;
        }

        /**
         * Target @s, @e : NULL, why?
         * Target player : sometimes no result, why?
         */

        [Command(Name = "gamemode", Aliases = new[] { "gm" }, Description = "플레이어를 특정 게임 모드로 변경합니다.")]
        public void GameMode_Execute(Player sender, GameMode gameMode, Target player = null)
        {
            Player targetPlayer = sender;

            if(player != null)
                targetPlayer = player.Players.FirstOrDefault();

            if(targetPlayer == null)
            {
                sender.SendMessage("§c플레이어를 찾을 수 없습니다.");
                return;
            }

            if (!Enum.IsDefined(typeof(GameMode), gameMode))
            {
                sender.SendMessage("§c알 수 없는 게임 모드입니다.");
                return;
            }

            targetPlayer.SetGameMode(gameMode);

            if(sender == targetPlayer)
            {
                sender.SendMessage($"내 게임 모드를 {(GameModeName)gameMode} 모드(으)로 변경했습니다.");
            }
            else
            {
                sender.SendMessage($"{targetPlayer.Username}님의 게임 모드를 {(GameModeName)gameMode} 모드(으)로 변경했습니다.");
                targetPlayer.SendMessage($"게임 모드가 {(GameModeName)gameMode} 모드(으)로 업데이트되었습니다.");
            }

        }

        [Command(Name = "gamemode", Aliases = new[] { "gm" }, Description = "플레이어를 특정 게임 모드로 변경합니다.")]
        public void Int_Execute(Player sender, int gameMode, Target player = null)
        {

            Player targetPlayer = sender;

            if(player != null)
                targetPlayer = player.Players.FirstOrDefault();

            if(targetPlayer == null)
            {
                sender.SendMessage("§c플레이어를 찾을 수 없습니다.");
                return;
            }

            if(!Enum.IsDefined(typeof(GameMode), (GameMode)gameMode))
            {
                sender.SendMessage("§c알 수 없는 게임 모드입니다.");
                return;
            }

            targetPlayer.SetGameMode((GameMode)gameMode);

            if(sender == targetPlayer)
            {
                sender.SendMessage($"내 게임 모드를 {(GameModeName)(GameMode)gameMode} 모드(으)로 변경했습니다.");
            }
            else
            {
                sender.SendMessage($"{targetPlayer.Username}님의 게임 모드를 {(GameModeName)(GameMode)gameMode} 모드(으)로 변경했습니다.");
                targetPlayer.SendMessage($"게임 모드가 {(GameModeName)(GameMode)gameMode} 모드(으)로 업데이트되었습니다.");
            }

        }

        public enum GameModeName
        {
            서바이벌 = 0,
            크리에이티브 = 1,
            모험 = 2,
            관람자 = 3
        }

    }

}
